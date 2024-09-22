<!DOCTYPE html>
<html>
<head>
  <title>Weather App</title>
  <style>
  /* import font from googlr font api */
@import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700;800&family=Unbounded:wght@200;400;700&display=swap");
/* Style for form container */
form { 
  display: flex; 
  justify-content: center; 
  align-items: center; 
  margin: 15px; 
} 

/* Style for text input field */
input[type=text] { 
  padding: 8px; 
  font-size: 13px; 
  border-radius: 3 px; 
  margin-right: 10px; 
   width: 50%;
} 

/* Style for submit button */
input[type=submit] { 
  background-color: #4CAF50; 
  color: white; 
  border-radius: 5px; 
  border: none; 
  padding: 10px 20px; 
  font-size: 16px; 
  cursor: pointer; 
}

/* Style for heading */
h1 { 
  margin: 0;
    text-align: center;
    color: #333;
	font-family: "Unbounded", cursive;
  font-size: 25px;
} 

/* Style for table */
table {
  margin-top: 20px;
  border-collapse: separate;
  border-spacing: 0;
  width: 100%;
  background-color: #fff;
  color: #333;
  font-size: 16px;
  border-radius: 5px;
  overflow: hidden;
}

/* style for table rows */
th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #eee;
  font-weight: bold;
}

tr:nth-child(even) {
  background-color: #f2f2f2;
}
/* style for button */
.button{
    background-color:#21c452;
    color:aliceblue;
    text-decoration: none;
    border:2px solid transparent;
    font-weight: bold;
    padding: 10px 30px;
    border-radius: 30px;
    transition: transform .4s;
 }


 .button:hover{
    border: 2px solid #00f921;
    cursor:pointer;
 }

  </style>
</head>
 <body>
 <!-- This HTML form sends information to the current page using the "get" method -->
  <form method="get" action="">
  <!-- This text input area asks the user to provide the name of the city. -->
    <input type="text" name="city" placeholder="Enter city name">
    <!-- This button, labeled "submit," submits the form. -->
    <input type="submit" name="submit" value="Search">
  </form>
   <?php
  //  This code checks to see if the "submit" button on the form has been clicked.
  if (isset($_GET['submit'])) {
    // If the button was clicked, the code modifies the value of $city to the value entered in the form's input field.
    $city = $_GET['city'];
    // If the button is not pressed, the value of $city is set to "Phenix" by default.
  } else {
    $city = "Phenix";
  }
  // api yo request weather information from the OpenWeatherMap API.
   $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid=abfc123a5ce928e13e6bdde15ada5fc9&units=metric";
   // The OpenWeatherMap API is used in this line of code to retrieve the weather information using file_get_contents().
  $response = file_get_contents($url);
  // The code in this line converts the JSON data obtained from the API into a PHP associative array.
  $data = json_decode($response, true);
   if (!$data) {
    //error handling of API
    die("Error: Failed to retrieve data from OpenWeatherMap API.");
  }
  // These lines of code parse the JSON array given by the OpenWeatherMap API and extract specific weather information.
  $city_name = $data['name'];
  $description = $data['weather'][0]['main'];
  $temperature = $data['main']['temp'];
  $pressure = $data['main']['pressure'];
  $humidity = $data['main']['humidity'];
  $wind_speed = $data['wind']['speed'];
  $wind_direction = $data['wind']['deg'];
  $cloudiness = $data['clouds']['all'];
  $sunrise = date('Y-m-d H:i:s', $data['sys']['sunrise']);
  $sunset = date('Y-m-d H:i:s', $data['sys']['sunset']);
  $rainfall = isset($data['rain']['1h']) ? $data['rain']['1h'] : 'not given';
   //  These lines of code use the mysqli_connect() function to establish a connection to a MySQL database.
  $host = 'localhost';
  $username = 'root';
  $password = '';
  $dbname = 'prototype';
   $conn = mysqli_connect($host, $username, $password, $dbname);
   if (!$conn) {
    //The code generates an error message and stops script execution if the connection failed
    die("Connection failed: " . mysqli_connect_error());
  }
//  retrieve the most recent weather information for a specific city and contain information from the current hour using the current date and time.
  $sql = "SELECT * FROM weatherdata WHERE city='$city_name' AND DATE(date) = CURDATE()";
   $result = mysqli_query($conn, $sql);
   //Using mysqli_num_rows(), determine whether the query produced any rows. Update the current record with the most recent weather data if there are rows. Insert a new record with the most recent weather data if there are no rows.
   if (mysqli_num_rows($result) > 0) {
    // Update existing row with latest weather data
    $sql = "UPDATE weatherdata SET `description`='$description',temperature='$temperature', rainfall=0, humidity='$humidity', wind_speed='$wind_speed' , pressure = '$pressure' WHERE city='$city_name' AND date= DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')";
  } else {
    // Insert new row with current weather data
    $sql = "INSERT INTO weatherdata (city, date, `description`,  temperature, rainfall, wind_speed, humidity, pressure)
          VALUES ('$city_name', NOW(), '$description', '$temperature', '0', '$wind_speed', '$humidity', '$pressure')";
  }
 
   // Retrieve latest weather data from database
  $sql = "SELECT * FROM weatherdata WHERE city='$city_name' ORDER BY date DESC LIMIT 7";//retrieve weather information for a certain city,sort the results by date in ascending order and display last seven record

  $result = mysqli_query($conn, $sql);
   echo "<h1>Weekly weather data of {$city_name} </h1>";
  echo "<table border='1'>";
  echo "<tr>";
  echo "<th>Date</th>";
  echo "<th>Condition</th>";
  echo "<th>Temperature</th>";
  echo "<th>Humidity</th>";
  echo "<th>Wind_speed</th>";
  echo "<th>Pressure</th>";
  echo "</tr>";
  // displays data from  database in the form of an HTML table. The while loop receives information from the query result row by row until there are no more rows left to fetch
   while ($row = mysqli_fetch_assoc($result)) {
    $date = date('Y-m-d', strtotime($row['date']));
    $condition = $row['description'];
    $temperature = $row['temperature'];
    $humidity = $row['humidity'];
    $wind_speed = $row['wind_speed'];
    $pressure = $row['pressure'];
     echo "<tr>";
    echo "<td>{$date}</td>";
    echo "<td>{$description}</td>";
    echo "<td>{$temperature}°C</td>";
    echo "<td>{$humidity}%</td>";
    echo "<td>{$wind_speed} m/s</td>";
    echo "<td>{$pressure}</td>";
    echo "</tr>";
  }
   echo "</table>";
   if (mysqli_query($conn, $sql)) {
    echo "<p>Data saved successfully!</p>"; // Message to indicate successful save of data
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
   // Closing the database connection
  mysqli_close($conn);
  ?>
  <!-- line break -->
  <br>
  <!-- linking the html with this php -->
  <a class="button" href="ShubhamShrestha_2329470.html">Return</a>
</body>
 </html>