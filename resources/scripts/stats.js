$(document).ready(function() {
    tracksCall = "top/tracks?time_range=long_term&limit=50";
    artistsCall = "top/artists?time_range=long_term&limit=50";

    table = document.querySelector('table');

    callAPI(tracksCall, table);
});

function callAPI(apiCall, table){
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
            var tracksArr = [];
            var artistsArr = [];
            console.log("Data: ", data);
            //data will have the JSON string from the return of apiCall
            const response = JSON.parse(data);
            console.log("Response: ", response);

            if (response.error === 'redirect') {
                alert("Please log in to Spotify to view your Wrapped.");
                window.location.href = './index.php';
            }

            //Setting the info
            console.log("Tracks/Artists");
            getTracks(response, tracksArr, artistsArr);

            addToTable(tracksArr, artistsArr, table);

        }, error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error: ' + textStatus);
            console.error('HTTP status: ' + jqXHR.status);
            console.error('Error thrown: ' + errorThrown);
        }
    });

}

function addToTable(tracksArr, artistsArr, table){
    console.log("add to table");
    let max = Math.max(tracksArr.length, artistsArr.length);
    for(let i = 0; i < max; i++){
        console.log("loop");
        const row = table.insertRow();
        const cell1 = row.insertCell(0);
        const cell2 = row.insertCell(1);
        const cell3 = row.insertCell(2);

        cell1.textContent = i + 1;
        if(tracksArr[i]){
            cell2.textContent = tracksArr[i];
        } 
        if(artistsArr[i]){
            cell3.textContent = artistsArr[i];
        }
    }
}

function getTracks(data, tracksArr, artistsArr) {
    for (let i = 0; i < data.items.length; i++) {
        console.log(data.items[i].name);
        tracksArr.push(data.items[i].name);
        artistsArr.push(data.items[i].artists[0].name);
    }
}
