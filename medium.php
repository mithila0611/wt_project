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
        <h3>Level :</h3><h5>Medium</h5>
        <form id="quizForm"></form>
        <button type="button" onclick="calculateScore()">Submit</button>
        <h3 id="result"></h3>
        <h3 id="corrections"></h3>
    </div>

    <script>
        const questions = [
    { q: "Which planet is known as the Red Planet?", options: ["Venus", "Mars", "Jupiter", "Saturn"], answer: "Mars" },
    { q: "Who discovered gravity?", options: ["Albert Einstein", "Isaac Newton", "Galileo Galilei", "Nikola Tesla"], answer: "Isaac Newton" },
    { q: "What is the capital of Canada?", options: ["Toronto", "Ottawa", "Vancouver", "Montreal"], answer: "Ottawa" },
    { q: "What is the chemical symbol for gold?", options: ["Ag", "Au", "Pb", "Fe"], answer: "Au" },
    { q: "Which is the longest river in the world?", options: ["Amazon", "Nile", "Yangtze", "Mississippi"], answer: "Nile" },
    { q: "Which continent has the most countries?", options: ["Asia", "Africa", "Europe", "South America"], answer: "Africa" },
    { q: "What is the capital of Australia?", options: ["Sydney", "Melbourne", "Canberra", "Brisbane"], answer: "Canberra" },
    { q: "Who invented the telephone?", options: ["Alexander Graham Bell", "Thomas Edison", "Nikola Tesla", "James Watt"], answer: "Alexander Graham Bell" },
    { q: "Which gas is most abundant in the Earth's atmosphere?", options: ["Oxygen", "Carbon Dioxide", "Nitrogen", "Argon"], answer: "Nitrogen" },
    { q: "Which famous scientist developed the theory of relativity?", options: ["Isaac Newton", "Albert Einstein", "Galileo Galilei", "Stephen Hawking"], answer: "Albert Einstein" },
    { q: "What is the largest organ in the human body?", options: ["Heart", "Liver", "Skin", "Brain"], answer: "Skin" },
    { q: "Which is the smallest planet in the solar system?", options: ["Mercury", "Mars", "Venus", "Pluto"], answer: "Mercury" },
    { q: "Which ocean is the largest?", options: ["Atlantic Ocean", "Indian Ocean", "Arctic Ocean", "Pacific Ocean"], answer: "Pacific Ocean" },
    { q: "Which country is known as the Land of the Rising Sun?", options: ["China", "Japan", "South Korea", "Thailand"], answer: "Japan" },
    { q: "Which metal is the best conductor of electricity?", options: ["Gold", "Copper", "Aluminum", "Silver"], answer: "Silver" },
    { q: "Which is the largest desert in the world?", options: ["Gobi Desert", "Sahara Desert", "Kalahari Desert", "Antarctic Desert"], answer: "Antarctic Desert" },
    { q: "Which country gifted the Statue of Liberty to the USA?", options: ["France", "United Kingdom", "Germany", "Italy"], answer: "France" },
    { q: "How many bones are there in an adult human body?", options: ["200", "206", "212", "215"], answer: "206" },
    { q: "What is the hardest natural substance on Earth?", options: ["Gold", "Iron", "Diamond", "Quartz"], answer: "Diamond" },
    { q: "Which is the world's highest mountain?", options: ["K2", "Kangchenjunga", "Mount Everest", "Makalu"], answer: "Mount Everest" },
    { q: "Which bird is known for its ability to mimic sounds?", options: ["Eagle", "Parrot", "Sparrow", "Owl"], answer: "Parrot" },
    { q: "What is the national currency of Japan?", options: ["Yuan", "Won", "Baht", "Yen"], answer: "Yen" },
    { q: "Which is the only mammal capable of true flight?", options: ["Flying Squirrel", "Bat", "Kangaroo", "Penguin"], answer: "Bat" },
    { q: "Which sport is known as the 'King of Sports'?", options: ["Cricket", "Football", "Tennis", "Hockey"], answer: "Football" },
    { q: "Who painted the famous artwork 'Mona Lisa'?", options: ["Vincent van Gogh", "Pablo Picasso", "Leonardo da Vinci", "Michelangelo"], answer: "Leonardo da Vinci" },
    { q: "Which is the largest island in the world?", options: ["Madagascar", "Greenland", "Borneo", "New Guinea"], answer: "Greenland" },
    { q: "Which blood type is known as the universal donor?", options: ["A", "B", "O", "AB"], answer: "O" },
    { q: "Which organ purifies blood in the human body?", options: ["Lungs", "Heart", "Liver", "Kidney"], answer: "Kidney" },
    { q: "Which gas is commonly known as laughing gas?", options: ["Nitrogen", "Oxygen", "Nitrous Oxide", "Carbon Dioxide"], answer: "Nitrous Oxide" },
    { q: "Which is the largest lake in the world?", options: ["Lake Superior", "Caspian Sea", "Lake Victoria", "Lake Baikal"], answer: "Caspian Sea" },
    { q: "Which element is found in all organic compounds?", options: ["Oxygen", "Carbon", "Nitrogen", "Hydrogen"], answer: "Carbon" },
    { q: "What is the capital of Brazil?", options: ["Rio de Janeiro", "São Paulo", "Brasília", "Buenos Aires"], answer: "Brasília" },
    { q: "Which is the most spoken language in the world?", options: ["English", "Hindi", "Mandarin Chinese", "Spanish"], answer: "Mandarin Chinese" },
    { q: "Which Indian city is known as the Silicon Valley of India?", options: ["Hyderabad", "Chennai", "Bangalore", "Pune"], answer: "Bangalore" },
    { q: "Which is the fastest land animal?", options: ["Tiger", "Cheetah", "Lion", "Leopard"], answer: "Cheetah" },
    { q: "Which is the smallest ocean in the world?", options: ["Atlantic Ocean", "Indian Ocean", "Arctic Ocean", "Pacific Ocean"], answer: "Arctic Ocean" },
    { q: "Which vitamin is produced when the skin is exposed to sunlight?", options: ["Vitamin A", "Vitamin B", "Vitamin C", "Vitamin D"], answer: "Vitamin D" },
    { q: "Which is the most abundant metal in the Earth's crust?", options: ["Iron", "Copper", "Aluminum", "Zinc"], answer: "Aluminum" },
    { q: "Who wrote the play 'Hamlet'?", options: ["Charles Dickens", "Jane Austen", "William Shakespeare", "Mark Twain"], answer: "William Shakespeare" },
    { q: "Which country has the most time zones?", options: ["Russia", "USA", "France", "China"], answer: "France" },
    { q: "Which is the national animal of India?", options: ["Elephant", "Lion", "Bengal Tiger", "Peacock"], answer: "Bengal Tiger" },
    { q: "Who discovered penicillin?", options: ["Alexander Fleming", "Louis Pasteur", "Joseph Lister", "Edward Jenner"], answer: "Alexander Fleming" },
    { q: "Which is the coldest planet in the solar system?", options: ["Neptune", "Uranus", "Pluto", "Saturn"], answer: "Neptune" }
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
            xhr.open("POST", "store_resultsmedium.php", true);
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
