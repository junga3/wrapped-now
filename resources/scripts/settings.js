var user = null;

$(document).ready(function () {
   // temp will have the string of api call https://api.spotify.com/v1/me
   let temp = "{}";
   let response = JSON.parse(temp);
   callAPI("", "username", 0);
   $('#username').html(user + "'s Wrapped");

   $('#bgcolor').on('change', function () {
      var optionSelected = $('option:selected', this);
      var valueSelected = this.value;     //hex color code
      $('.summary').css("background-color", valueSelected);
   });

   $('#textcolor').on('change', function () {
      var optionSelected = $('option:selected', this);
      var valueSelected = this.value;     //hex color code
      $('.summary').css("color", valueSelected);
   });

   $('#go').click(function () {
      const timeFrame = document.getElementById('time_range').value;
      //what type will be used in the api call
      let dataType, callType;
      dataType = document.getElementById('data_type').value;
      if (dataType == "artist" || dataType == "genre")
         callType = "artists";
      else
         callType = "tracks";

      const count = 5; //may let user choose
      const apiCall = `top/` + callType + `?time_range=` + timeFrame;
      console.log("API Call: ", apiCall);
      callAPI(apiCall, dataType, count);
   });

});

function callAPI(apiCall, dataType, count){
   var urlParams = new URLSearchParams(window.location.search);
   var code = urlParams.get('code');
   $.ajax({
      url: './resources/scripts/apiCall.php',
      type: 'POST',
      data: {
         apiCall: apiCall,
         code: code
      },
      
      success: function(data){
         console.log("Data: ", data);
         //data will have the JSON string from the return of apiCall
         const response = JSON.parse(data);
         console.log("Response: ", response);

         if (response.error === 'redirect') {
            alert("Please log in to Spotify to view your Wrapped.");
            window.location.href = './index.php';
         }

         //Setting the image
         var img = document.getElementById('user-photo');
         if(dataType != "username"){   
            if (dataType == "artist" || dataType == "genre") {
               img.src = response.items[0].images[0].url;
            } else {
               img.src = response.items[0].album.images[0].url;
            }
         }

         //Setting the info
         let info;
         console.log("Data Type: ", dataType);
         if (dataType == "track" || dataType == "artist") {
            console.log("Tracks/Artists");
            info = getTrackOrArtist(count, response);
         } else if (dataType == "album") {
            console.log("Albums");
            info = getAlbums(count, response);
         } else if(dataType == "username"){
            console.log("User: " + response.display_name);
            user = response.display_name;
            $('#username').html(user + "'s Wrapped");
         } else {
            console.log("Genres");
            info = getGenres(count, response);
         }
         $('#data').empty();
         addListItems(count, info);

      }, error: function(jqXHR, textStatus, errorThrown) {
         console.error('Error: ' + textStatus);
         console.error('HTTP status: ' + jqXHR.status);
         console.error('Error thrown: ' + errorThrown);
   }
   });
}

document.addEventListener('DOMContentLoaded', (event) => {
   const timeRangeSelect = document.getElementById('time_range');
   document.getElementById("top-title").innerHTML = "for the last month";
   document.getElementById("data-title").innerHTML = "Top Tracks";

   // Listen for changes to the TIME element
   timeRangeSelect.addEventListener('change', function () {
      if (timeRangeSelect.value == "short_term") {
         document.getElementById("top-title").innerHTML = "for the last month";
      }

      else if (timeRangeSelect.value == "medium_term") {
         document.getElementById("top-title").innerHTML = "for the last 6 months";
      }

      else {
         document.getElementById("top-title").innerHTML = "for the last year";
      }
   });

   const dataType = document.getElementById('data_type');
   dataType.addEventListener('change', function () {
      if (dataType.value == "track") {
         document.getElementById("data-title").innerHTML = "Top Tracks";
      } else if (dataType.value == "artist") {
         document.getElementById("data-title").innerHTML = "Top Artists";
      } else if (dataType.value == "album") {
         document.getElementById("data-title").innerHTML = "Top Albums";
      } else {
         document.getElementById("data-title").innerHTML = "Top Genres";
      }
   });

});

function addListItems(count, data) {
   for (let i = 0; i < count; i++) {
      $('#data').append('<li>' + data[i] + '</li>');
   }
}

function getTrackOrArtist(count, data) {
   let returnData = []
   for (let i = 0; i < count; i++) {
      returnData.push(data.items[i].name);
   }

   return returnData;
}

function getGenres(count, data) {
   let returnData = new Set();
   let i = 0;
   console.log(data.items[0].genres[0]);
   while (returnData.size != count) {
      returnData.add(data.items[i].genres[0]);
      i++;
   }


   return [...returnData];
}

function getAlbums(count, data) {
   let returnData = new Set();
   let i = 0;
   while (returnData.size != count) {
      if (data.items[i].album.album_type == "SINGLE") {
         i++;
         continue;
      }
      returnData.add(data.items[i].album.name);
      i++;
   }

   return [...returnData];
}

