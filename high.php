<?php
session_start();
include 'db_connection.php';


if (!isset($_SESSION['student_logged_in'])) {
    header("Location: studentLogin.php");
    exit();
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCQ Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        input[type="text"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        button:hover {
            background: #218838;
        }
        #login, #quiz {
            display: none;
        }
        .question {
            text-align: left;
            margin-bottom: 15px;
        }
        .question p {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>MCQ Test</h2>
        <h3>Level :</h3><h5>High</h5>
        <form id="quizForm"></form>
        <button type="button" onclick="calculateScore()">Submit</button>
        <h3 id="result"></h3>
        <h3 id="corrections"></h3>
    </div>
    <script>
        const questions = [
            { q:  "Which physicist is credited with the development of quantum mechanics?", options: ["Albert Einstein", "Max Planck", "Isaac Newton", "Niels Bohr"], answer: "Max Planck" },
            { q: "What is the largest known galaxy in the universe?", options: ["Milky Way", "Andromeda", "IC 1101", "Messier 87"], answer: "IC 1101" },
            { q: "Who formulated the General Theory of Relativity?", options: ["Isaac Newton", "Albert Einstein", "Galileo Galilei", "Stephen Hawking"], answer: "Albert Einstein" },
            { q: "Which particle is responsible for giving mass to other particles?", options: ["Photon", "Higgs Boson", "Neutrino", "Electron"], answer: "Higgs Boson" },
            { q: "Which civilization is credited with the earliest known writing system?", options: ["Egyptians", "Chinese", "Sumerians", "Indus Valley"], answer: "Sumerians" },
            { q: "Which treaty ended the First World War?", options: ["Treaty of Versailles", "Treaty of Paris", "Treaty of Tordesillas", "Treaty of Vienna"], answer: "Treaty of Versailles" },
            { q: "What is the SI unit of electric charge?", options: ["Watt", "Volt", "Coulomb", "Ohm"], answer: "Coulomb" },
            { q: "Which enzyme is responsible for DNA replication?", options: ["Ligase", "Helicase", "Polymerase", "Lipase"], answer: "Polymerase" },
            { q: "Which element has the highest melting point?", options: ["Iron", "Tungsten", "Carbon", "Platinum"], answer: "Tungsten" },
            { q: "Which country launched the first artificial satellite?", options: ["USA", "USSR", "China", "Germany"], answer: "USSR" },
            { q: "What is the most abundant gas in Earth's atmosphere?", options: ["Oxygen", "Carbon Dioxide", "Nitrogen", "Argon"], answer: "Nitrogen" },
            { q: "Which city hosted the first modern Olympic Games?", options: ["Paris", "Athens", "London", "Rome"], answer: "Athens" },
            { q: "Which ancient wonder was located in Babylon?", options: ["Great Pyramid of Giza", "Hanging Gardens", "Statue of Zeus", "Colossus of Rhodes"], answer: "Hanging Gardens" },
            { q: "Who discovered the laws of planetary motion?", options: ["Galileo Galilei", "Johannes Kepler", "Isaac Newton", "Tycho Brahe"], answer: "Johannes Kepler" },
            { q: "What is the largest desert in the world?", options: ["Sahara", "Antarctic", "Gobi", "Kalahari"], answer: "Antarctic" },
            { q: "Which country has the most time zones?", options: ["USA", "Russia", "France", "China"], answer: "France" },
            { q: "What is the heaviest naturally occurring element?", options: ["Uranium", "Plutonium", "Osmium", "Lead"], answer: "Uranium" },
            { q: "Which scientist proposed the Big Bang theory?", options: ["Albert Einstein", "Georges Lemaître", "Stephen Hawking", "Edwin Hubble"], answer: "Georges Lemaître" },
            { q: "Which is the only mammal capable of sustained flight?", options: ["Flying Squirrel", "Bat", "Colugo", "Sugar Glider"], answer: "Bat" },
            { q: "Which is the deepest ocean trench?", options: ["Tonga Trench", "Mariana Trench", "Java Trench", "Puerto Rico Trench"], answer: "Mariana Trench" },
            { q: "What is the chemical formula for ozone?", options: ["O₂", "O₃", "CO₂", "H₂O"], answer: "O₃" },
            { q: "Who was the first emperor of China?", options: ["Han Wudi", "Qin Shi Huang", "Kublai Khan", "Sun Yat-sen"], answer: "Qin Shi Huang" },
            { q: "What is the speed of light in vacuum?", options: ["300,000 km/s", "150,000 km/s", "1,000,000 km/s", "100,000 km/s"], answer: "300,000 km/s" },
            { q: "Which is the smallest bone in the human body?", options: ["Femur", "Tibia", "Stapes", "Ulna"], answer: "Stapes" },
            { q: "Who discovered penicillin?", options: ["Alexander Fleming", "Louis Pasteur", "Robert Koch", "Joseph Lister"], answer: "Alexander Fleming" },
            { q: "Which planet has the most moons?", options: ["Earth", "Saturn", "Jupiter", "Neptune"], answer: "Saturn" },
            { q: "What is the most common type of star in the universe?", options: ["Neutron Star", "Red Dwarf", "White Dwarf", "Blue Giant"], answer: "Red Dwarf" },
            { q: "Who painted the ceiling of the Sistine Chapel?", options: ["Leonardo da Vinci", "Raphael", "Michelangelo", "Donatello"], answer: "Michelangelo" },
            { q: "Which is the longest river in the world?", options: ["Amazon", "Nile", "Yangtze", "Mississippi"], answer: "Nile" },
            { q: "What is the only metal that is liquid at room temperature?", options: ["Mercury", "Gallium", "Cesium", "Bromine"], answer: "Mercury" },
            { q: "Which country has the highest number of nuclear reactors?", options: ["Russia", "USA", "China", "France"], answer: "USA" },
            { q: "Who wrote 'The Origin of Species'?", options: ["Gregor Mendel", "Charles Darwin", "Alfred Wallace", "Louis Pasteur"], answer: "Charles Darwin" },
            { q: "What is the deepest lake in the world?", options: ["Caspian Sea", "Lake Baikal", "Lake Tanganyika", "Lake Superior"], answer: "Lake Baikal" },
            { q: "Which ancient city is known as the 'Lost City of the Incas'?", options: ["Machu Picchu", "Tenochtitlán", "Petra", "Angkor Wat"], answer: "Machu Picchu" },
            { q: "Which element is named after a planet?", options: ["Plutonium", "Uranium", "Neptunium", "Both Uranium and Neptunium"], answer: "Both Uranium and Neptunium" },
            { q: "Who was the first person to walk in space?", options: ["Neil Armstrong", "Yuri Gagarin", "Alexei Leonov", "Buzz Aldrin"], answer: "Alexei Leonov" },
            { q: "Which continent has the most countries?", options: ["Asia", "Europe", "Africa", "North America"], answer: "Africa" },
            { q: "Which is the largest active volcano in the world?", options: ["Mauna Loa", "Mount Etna", "Krakatoa", "Mount St. Helens"], answer: "Mauna Loa" }
        ];
        
        let selectedQuestions = [];

        function generateQuiz() {
            selectedQuestions = questions.sort(() => 0.5 - Math.random()).slice(0, 10);
            let quizForm = document.getElementById("quizForm");
            quizForm.innerHTML = "";

            selectedQuestions.forEach((item, index) => {
                let questionHTML = `<div class='question'>
                    <p>${index + 1}. ${item.q}</p>`;
                item.options.forEach(option => {
                    questionHTML += `<input type='radio' name='q${index}' value='${option}'> ${option}<br>`;
                });
                questionHTML += `</div>`;
                quizForm.innerHTML += questionHTML;
            });
        }

        let timer = 600; // 10 minutes
        let startTime = new Date().getTime(); // Capture start time

        function startTimer() {
            let timerDisplay = document.createElement("h3");
            timerDisplay.id = "timer";
            document.querySelector(".container").prepend(timerDisplay);

            let countdown = setInterval(function() {
                let minutes = Math.floor(timer / 60);
                let seconds = timer % 60;
                timerDisplay.innerHTML = `Time Left: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                timer--;

                if (timer < 0) {
                    clearInterval(countdown);
                    calculateScore(); // Auto-submit when time is up
                }
            }, 1000);
        }

        function calculateScore() {
            let form = document.getElementById("quizForm");
            let score = 0;
            let feedback = "";

            selectedQuestions.forEach((item, index) => {
                let selected = form.elements[`q${index}`];
                if (selected && selected.value === item.answer) {
                    score=score+10;
                } else {
                    feedback += `<p>Correct answer for Q${index + 1}: ${item.answer}</p>`;
                }
            });

            let endTime = new Date().getTime();
            let timeTaken = Math.floor((endTime - startTime) / 1000); // Time in seconds

            document.getElementById("result").innerHTML = `You scored: ${score} / 100`;
            document.getElementById("corrections").innerHTML = feedback;

            // Send Data to Server
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "store_resultshigh.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send(`score=${encodeURIComponent(score)}&time_taken=${encodeURIComponent(timeTaken)}`);
        }

        // Ensure both functions are called on page load
        window.onload = function() {
            generateQuiz();
            startTimer();
        };

                
        
    </script>
</body>
</html>
