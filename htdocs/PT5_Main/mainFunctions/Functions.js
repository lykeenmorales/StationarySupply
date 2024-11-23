function SendAJXCallback(DataToSent) {
    if (!(DataToSent instanceof Object)) {
        throw new TypeError("DataToSent{Arg} must be an instance of Object");
    }
    
    $.ajax({
        url: '../mainFunctions/ajxReceiver.php', // PHP script update
        type: 'POST', // Send data via POST method
        contentType: 'application/json',
        data: JSON.stringify(DataToSent), // Data being sent to PHP
        success: function(response) {
            console.log("Data Received by the Server: " + response);
        },
        error: function(xhr, status, error) {
            console.error("Error while receiving Data: " + error);
        }
    });
}

function SendMainAJXCallback(DataToSent) {
    if (!(DataToSent instanceof Object)) {
        throw new TypeError("DataToSent{Arg} must be an instance of Object");
    }
    
    $.ajax({
        url: './mainFunctions/ajxReceiver.php', // PHP script update
        type: 'POST', // Send data via POST method
        contentType: 'application/json',
        data: JSON.stringify(DataToSent), // Data being sent to PHP
        success: function(response) {
            console.log("Data Received by the Server: " + response);
        },
        error: function(xhr, status, error) {
            console.error("Error while receiving Data: " + error);
        }
    });
}

export {SendAJXCallback, SendMainAJXCallback}