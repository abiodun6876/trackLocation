<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <title>Visitor Information</title>
  <style>
    /* Styles for the card container and card omitted for brevity */
    body {
      background-color: #f1f1f1;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }

    .card-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      position: relative;
    }

    .profile-circle {
      width: 50px;
      height: 50px;
      background-color: #4CAF50;
      border-radius: 50%;
      position: absolute;
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
    }

    .card {
      width: 100%;
      max-width: 400px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      padding: 20px;
      margin: 20px;
    }

    .card-title {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .card-text {
      font-size: 16px;
      line-height: 1.5;
    }

    #map {
      height: 200px;
      margin-bottom: 20px;
      width: 100%;
    }

    .button-container {
      display: flex;
      justify-content: space-between;
    }

    .action-button {
      flex: 1;
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-right: 5px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: background-color 0.3s ease-in-out;
    }

    .action-button img {
      width: 24px;
      height: 24px;
      margin-bottom: 5px;
    }

    .action-button:hover {
      background-color: #45a049;
    }

    .tabs-container {
      margin-top: 20px;
      display: flex;
      justify-content: space-around;
    }

    .tab {
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease-in-out;
    }

    .tab:hover {
      background-color: #45a049;
    }

    .coordinates-container {
      display: none;
      flex-direction: column;
      align-items: center;
    }

    .saved-coordinates {
      margin-top: 20px;
    }

    #coordinates-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    #coordinates-table th, #coordinates-table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    #coordinates-table th {
      background-color: #4CAF50;
      color: white;
    }
  </style>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>
  <div class="card-container">
    <div class="profile-circle">U</div>
   
    <div class="card">
      <h5 class="card-title">Visitor Information</h5>
      <p class="card-text" id="visitor-info"></p>
      <p class="card-text" id="location-info"></p>
      <div id="map"></div>
      <div class="button-container">
        <button class="action-button" id="home-btn">
          <i class="fas fa-home"></i>
          <span>Home</span>
        </button>
        <button class="action-button" id="get-location-btn">
          <i class="fas fa-map-marker-alt"></i>
          <span>Get Location</span>
        </button>
        <button class="action-button" id="share-location-btn">
          <i class="fas fa-share"></i>
          <span>Share</span>
        </button>
        <button class="action-button" id="stop-tracking-btn">
          <i class="fas fa-stop"></i>
          <span>Stop Tracking</span>
        </button>
      </div>
      <div class="tabs-container">
        <button class="tab" id="show-coordinates-tab">Show Coordinates</button>
        <button class="tab" id="hide-coordinates-tab">Hide Coordinates</button>
      </div>
      <div class="coordinates-container" id="coordinates-container">
        <table id="coordinates-table">
          <thead>
            <tr>
              <th>Latitude</th>
              <th>Longitude</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody id="coordinates-body"></tbody>
        </table>
      </div>
      <div class="saved-coordinates" id="saved-coordinates"></div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    $(document).ready(function () {
      var map = L.map("map").setView([0, 0], 2);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
      }).addTo(map);

      var marker = L.marker([0, 0]).addTo(map);

      var latitude, longitude, accuracy;
      var coordinates = [];

      $.get("https://ipapi.co/json/", function (response) {
        var publicIp = response.ip;
        var country = response.country_name || "Unknown Country";
        var visitorInfo =
          "Public IP Address: " +
          publicIp +
          "<br>" +
          country;
        $("#visitor-info").html(visitorInfo);
      });

      $("#get-location-btn").on("click", function () {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(success, error, {
            enableHighAccuracy: true,
            maximumAge: 0,
          });
        } else {
          console.log("Geolocation is not supported by this browser.");
        }
      });

      $("#share-location-btn").on("click", function () {
        if (latitude && longitude) {
          var shareLink =
            "https://www.google.com/maps/place/" + latitude + "," + longitude;
          alert("Share this link to your location: " + shareLink);
        } else {
          alert("Location data not available.");
        }
      });

      $("#stop-tracking-btn").on("click", function () {
        alert("Tracking stopped.");
        clearInterval(trackingInterval);
      });

      $("#home-btn").on("click", function () {
        alert("Go to Home");
      });

      $("#show-coordinates-tab").on("click", function () {
        $(".coordinates-container").show();
        $("#saved-coordinates").hide();
        updateCoordinatesTable();
      });

      $("#hide-coordinates-tab").on("click", function () {
        $(".coordinates-container").hide();
        $("#saved-coordinates").show();
      });

      var trackingInterval = setInterval(trackLocation, 300000); // Track every 5 minutes

      function trackLocation() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function (position) {
            var timestamp = new Date().toISOString();
            coordinates.push([position.coords.latitude, position.coords.longitude, timestamp]);
            localStorage.setItem("trackedCoordinates", JSON.stringify(coordinates));
            updateCoordinatesTable();
          }, error, {
            enableHighAccuracy: true,
            maximumAge: 0,
          });
        } else {
          console.log("Geolocation is not supported by this browser.");
        }
      }

      function success(position) {
        latitude = position.coords.latitude;
        longitude = position.coords.longitude;
        accuracy = position.coords.accuracy;

        var locationInfo =
          "Latitude: " + latitude + "<br>" +
          "Longitude: " + longitude + "<br>" +
          "Accuracy: " + accuracy + " meters";
        $("#location-info").html(locationInfo);

        marker.setLatLng([latitude, longitude]).update();
        map.setView([latitude, longitude], map.getZoom());

        var timestamp = new Date().toISOString();
        coordinates.push([latitude, longitude, timestamp]);
        localStorage.setItem("trackedCoordinates", JSON.stringify(coordinates));
        updateCoordinatesTable();
      }

      function error() {
        console.log("Unable to retrieve the location.");
      }

      function updateCoordinatesTable() {
        var coordinatesBody = $("#coordinates-body");
        coordinatesBody.empty();

        if (coordinates.length > 0) {
          coordinates.forEach(function (coord) {
            var row = "<tr><td>" + coord[0] + "</td><td>" + coord[1] + "</td><td>" + coord[2] + "</td></tr>";
            coordinatesBody.append(row);
          });
        } else {
          coordinatesBody.append("<tr><td colspan='3'>No coordinates available</td></tr>");
        }
      }
    });
  </script>
</body>
</html>
