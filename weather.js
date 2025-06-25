const apikey="a4dbba36a26ac8db77c3bf98e07cf4e0";
const apiurl="https://api.openweathermap.org/data/2.5/weather?units=metric&q=";

const searchcity=document.querySelector(".searchhotel  #district");
const searchbtn=document.querySelector(".searchhotel  button");
const weatherIcon=document.querySelector(".weather-icon");
console.log("heyyy mannn city elem");
console.log(searchcity);
console.log(" city value");
console.log(searchcity.value);
console.log(searchbtn);
console.log(weatherIcon);

async function checkWeather(city){
    console.log("ciytytyyy is");
    console.log(city);
    const response= await fetch(apiurl+city+`&appid=${apikey}`);
    var data=await response.json();

    console.log(data);

    document.querySelector(".city").innerHTML = data.name;
    document.querySelector(".temp").innerHTML = data.main.temp + "°c";
    document.querySelector(".humidity").innerHTML = data.main.humidity + "%";
    document.querySelector(".wind").innerHTML = data.wind.speed + " km/hr";

    if(data.weather[0].main == "Clouds"){
        weatherIcon.src= "images/clouds.png";
    }
    else if(data.weather[0].main == "Clear"){
        weatherIcon.src= "images/clear.png";
    }
    else if(data.weather[0].main == "Rain"){
        weatherIcon.src= "images/rain.png";
    }
    else if(data.weather[0].main == "Drizzle"){
        weatherIcon.src= "images/drizzle.png";
    }
    else if(data.weather[0].main == "Mist"){
        weatherIcon.src= "images/mist.png";
    }
    else  if(data.weather[0].main == "Snow"){
        weatherIcon.src= "images/snow.png";
    }
}
console.log("beforeevent");
window.checkWeather=checkWeather;
console.log("afterevent");