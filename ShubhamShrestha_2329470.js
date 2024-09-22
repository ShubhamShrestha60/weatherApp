const apiKey = "abfc123a5ce928e13e6bdde15ada5fc9";
const weatherUrl ="https://api.openweathermap.org/data/2.5/weather?q=phenix&appid=abfc123a5ce928e13e6bdde15ada5fc9&units=metric";

// formatting the data of temperarture and rainfall using function
function formatData(data) {
  if (data === undefined) {
    return 'N/A';
  } else {
    return `${data} ${String.fromCharCode(8451)}`;
  }
}

// this function upadtes the weather information on the webpage
async function updateWeatherInfo(data) {
  console.log(data);
  const cityName = data.name;
  const countr = data.sys.country; 
  const date = new Date().toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
  const weatherCondition = data.weather[0].description;
  const weatherIcon = `http://openweathermap.org/img/wn/${data.weather[0].icon}.png`;
  const temperature = formatData(data.main.temp);
  const rainfall = formatData(data.rain ? data.rain['1h'] : data.snow ? data.snow['1h'] : undefined);
  const windSpeed = `${data.wind.speed} m/s`;
  const humidity = `${data.main.humidity}%`;
  const pressure = `${data.main.pressure}m/h`;

  document.querySelector('.city-name').textContent = cityName +","+countr;
  document.querySelector('.date').textContent = date;
  document.querySelector('.weather-condition').textContent = weatherCondition;
  document.querySelector('.weather-icon img').src = weatherIcon;
  document.querySelector('.temperature').innerHTML = temperature;
  document.querySelector('.rainfall').innerHTML = 'Rainfall:      '+rainfall ;
  document.querySelector('.wind-speed').textContent ='WindSpeed:  '+windSpeed;
  document.querySelector('.humidity').textContent ='Humidity:     '+humidity;
  document.querySelector('.pressure').innerHTML = 'Pressure:      '+pressure;
}



// function for error handling using asyc await function
async function handleSearch() {
  document.querySelector('.error').textContent = "";
  const city = document.querySelector('input').value;
  const searchUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

  try {
    const response = await fetch(searchUrl);
    const data = await response.json();
    await updateWeatherInfo(data);
  } catch (error) {
    console.log(error);
    document.querySelector('.error').textContent = "Sorry, the city you entered could not be found. Please try again.";
  }
}



// whenever the page loads weather data is retrieved
async function getWeatherData() {
  try {
    const response = await fetch(weatherUrl);
    const data = await response.json();
    await updateWeatherInfo(data);
  } catch (error) {
    console.log(error);
  }
}

getWeatherData();

// Adding click event listener for  search button
document.querySelector('button').addEventListener('click', handleSearch);


