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
        <h3>Level :</h3><h5>Low</h5>
        <form id="quizForm"></form>
        <button type="button" onclick="calculateScore()">Submit</button>
        <h3 id="result"></h3>
        <h3 id="corrections"></h3>
    </div>

    <script>
        const questions = [
            { q: "Which ocean is the largest?", options: ["Atlantic", "Indian", "Arctic", "Pacific"], answer: "Pacific" },
            { q: "Which is the longest river in the world?", options: ["Amazon", "Nile", "Yangtze", "Mississippi"], answer: "Nile" },
            { q: "What is the hardest natural substance on Earth?", options: ["Gold", "Iron", "Diamond", "Quartz"], answer: "Diamond" },
            { q: "Which is the most abundant metal in the Earth's crust?", options: ["Iron", "Copper", "Aluminum", "Zinc"], answer: "Aluminum" },
            { q: "Which element is found in all organic compounds?", options: ["Oxygen", "Carbon", "Nitrogen", "Hydrogen"], answer: "Carbon" },
            { q: "What is the capital of France?", options: ["Berlin", "Madrid", "Paris", "Rome"], answer: "Paris" },
            { q: "Who discovered gravity?", options: ["Einstein", "Newton", "Galileo", "Hawking"], answer: "Newton" },
            { q: "What is the largest planet in our solar system?", options: ["Mars", "Jupiter", "Saturn", "Neptune"], answer: "Jupiter" },
            { q: "What is the chemical symbol for gold?", options: ["Ag", "Au", "Pt", "Fe"], answer: "Au" },
            { q: "What is the currency of Japan?", options: ["Yuan", "Won", "Rupee", "Yen"], answer: "Yen" },
            { q: "Which Indian state has the largest area?", options: ["Maharashtra", "Madhya Pradesh", "Rajasthan", "Uttar Pradesh"], answer: "Rajasthan" },
            { q: "Which state has the highest population in India?", options: ["Maharashtra", "Uttar Pradesh", "Bihar", "West Bengal"], answer: "Uttar Pradesh" },
            { q: "Which is the smallest state in India by area?", options: ["Sikkim", "Goa", "Manipur", "Mizoram"], answer: "Goa" },
            { q: "Which state is known as the <b>Land of Five Rivers</b>?",options: ["Rajasthan","Punjab","Haryana","Uttarakhand"], answer: "Punjab"},
            { q: "Which state is known as the 'Spice Garden of India'?", options: ["Kerala", "Karnataka", "Tamil Nadu", "Andhra Pradesh"],answer: "Kerala"},
            { q: "Which state is the largest producer of tea in India?", options: ["West Bengal", "Assam", "Kerala", "Karnataka"],answer: "Assam"},
            { q: "Which Indian state shares its border with the most countries?",options: ["Arunachal Pradesh", "Jammu & Kashmir", "West Bengal", "Sikkim"], answer: "Arunachal Pradesh" },
            { q: "Which Indian state was formed in 2014?",options: ["Chhattisgarh", "Jharkhand", "Telangana", "Uttarakhand"],answer: "Telangana"},
            { q: "Which is the only Indian state with three capitals?", options: ["Maharashtra", "Himachal Pradesh", "Andhra Pradesh", "Jammu & Kashmir"], answer: "Jammu & Kashmir"},
            { q: "Which Indian state has the largest number of Lok Sabha seats?", options: ["Bihar", "Maharashtra", "Uttar Pradesh", "West Bengal"], answer: "Uttar Pradesh"},
            { q: "Which state is the largest producer of wheat in India?", options: ["Punjab", "Haryana", "Uttar Pradesh", "Rajasthan"], answer: "Uttar Pradesh" },
            { q: "Which state is known as the 'Apple State of India'?", options: ["Jammu & Kashmir", "Himachal Pradesh", "Uttarakhand", "Arunachal Pradesh"], answer: "Himachal Pradesh"},
            { q: "Which state is famous for the 'Bihu Festival'?", options: ["Odisha", "Assam", "West Bengal", "Tripura"], answer: "Assam"},
            { q: "The Sun Temple at Konark is located in which state?", options: ["Maharashtra", "Gujarat", "Odisha", "Tamil Nadu"], answer: "Odisha"},
            { q: "Which Indian state is known for the 'Pushkar Fair'?",options: ["Rajasthan", "Madhya Pradesh", "Gujarat", "Uttar Pradesh"], answer: "Rajasthan"},
            { q: "Which Indian state has the highest literacy rate?", options: ["Maharashtra", "Kerala", "Tamil Nadu", "Himachal Pradesh"], answer: "Kerala"},
            { q: "The capital of Meghalaya is:", options: ["Imphal", "Aizawl", "Shillong", "Kohima"],answer: "Shillong" },
            { q: "Which Indian state is known as the 'Land of the Rising Sun'?", options: ["Sikkim", "Nagaland", "Arunachal Pradesh", "Mizoram"],answer: "Arunachal Pradesh"},
            { q: "Which state has the maximum forest cover in India?", options: ["Madhya Pradesh", "Arunachal Pradesh", "Kerala", "Assam"],answer: "Madhya Pradesh"},
            { q: "What is the currency of India?", options: ["Rupee", "Dollar", "Pound", "Dinar"], answer: "Rupee"},
            { q: "Which authority prints currency notes in India?", options: ["Ministry of Finance", "Reserve Bank of India (RBI)", "NITI Aayog", "SEBI"],answer: "Reserve Bank of India (RBI)"},
            { q: "The first paper currency in India was issued in which year?",options: ["1800", "1861", "1947", "1950"],answer: "1861"},
            { q: "Who issues one-rupee notes in India?", options: ["Reserve Bank of India", "Ministry of Finance", "SEBI", "Indian Government Printing Press"], answer: "Ministry of Finance"},
            { q: "Which is the highest denomination note ever printed in India?",options: ["₹500", "₹1000", "₹10,000", "₹2000"],answer: "₹10,000"},
            { q: "Which year saw the demonetization of ₹500 and ₹1000 notes?",options: ["2014", "2015", "2016", "2017"],answer: "2016"},
            { q: "What is the symbol of the Indian Rupee?",options: ["₹", "$", "€", "£"],answer: "₹"},
            { q: "Which governor of RBI introduced the ₹2000 note?",options: ["Raghuram Rajan", "Urjit Patel", "Shaktikanta Das", "D. Subbarao"],answer: "Urjit Patel"},
            { q: "What is the color of the ₹500 note introduced after demonetization?",options: ["Blue", "Orange", "Green", "Stone Grey"], answer: "Stone Grey"},
            { q: "Which animal is featured on the Indian currency notes?",options: ["Elephant", "Tiger", "Lion", "All of the above"],answer: "All of the above"},
            { q: "Which Indian currency note features Mangalyaan?",options: ["₹100", "₹200", "₹500", "₹2000"],answer: "₹2000"},
            { q: "The RBI prints Indian currency notes at how many locations?",options: ["2", "3", "4", "5"],answer: "4"},
            { q: "Which is the smallest denomination coin currently in circulation in India?",options: ["1 paisa", "10 paisa", "50 paisa", "₹1"],answer: "₹1"},
            { q: "Who built the Red Fort in Delhi?", options: ["Babur", "Akbar", "Shah Jahan", "Aurangzeb"], answer: "Shah Jahan"},
            { q: "The Vijayanagara Empire was founded in which year?", options: ["1320", "1336", "1351", "1368"], answer: "1336"},
            { q: "The First Battle of Panipat (1526) was fought between:",options: ["Babur and Rana Sanga", "Babur and Ibrahim Lodi", "Humayun and Sher Shah", "Akbar and Hemu"],answer: "Babur and Ibrahim Lodi"},
            { q: "Who introduced the Mansabdari system?",options: ["Akbar", "Aurangzeb", "Sher Shah Suri", "Jahangir"],answer: "Akbar"},
            { q: "The Permanent Settlement of 1793 was introduced by:",options: ["Lord Cornwallis", "Lord Wellesley", "Warren Hastings", "Robert Clive"],answer: "Lord Cornwallis"},
            { q: "The Swadeshi Movement started in:", options: ["1905", "1911", "1920", "1930"],answer: "1905"},
            { q: "Who was the Viceroy during the Partition of Bengal?",options: ["Lord Curzon", "Lord Mountbatten", "Lord Linlithgow", "Lord Wavell"],answer: "Lord Curzon"},
            { q: "Who led the Civil Disobedience Movement in 1930?",options: ["Mahatma Gandhi", "Subhas Chandra Bose", "Jawaharlal Nehru", "Sardar Patel"],answer: "Mahatma Gandhi"},
            { q: "The 'Quit India Movement' was launched in:",options: ["1919", "1929", "1942", "1947"],answer: "1942"},
            { q: "Who was the first President of Independent India?",options: ["Dr. Rajendra Prasad", "Jawaharlal Nehru", "Sardar Patel", "B.R. Ambedkar"],answer: "Dr. Rajendra Prasad"},
            { q: "India got independence in:",options: ["1945", "1947", "1950", "1962"],answer: "1947"},
            { q: "Who was the first Governor-General of independent India?",options: ["Lord Mountbatten", "C. Rajagopalachari", "Jawaharlal Nehru", "Sardar Patel"],answer: "Lord Mountbatten"}
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
            xhr.open("POST", "store_results.php", true);
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
