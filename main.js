///////////////////////////////////////////////////////////////////////////////////////////////////////
//
// Welcome to your first Cloud Script revision!
//
// Cloud Script runs in the PlayFab cloud and has full access to the PlayFab Game Server API 
// (https://api.playfab.com/Documentation/Server), and it runs in the context of a securely
// authenticated player, so you can use it to implement logic for your game that is safe from
// client-side exploits. 
//
// Cloud Script functions can also make web requests to external HTTP
// endpoints, such as a database or private API for your title, which makes them a flexible
// way to integrate with your existing backend systems.
//
// There are several different options for calling Cloud Script functions:
//
// 1) Your game client calls them directly using the "ExecuteCloudScript" API,
// passing in the function name and arguments in the request and receiving the 
// function return result in the response.
// (https://api.playfab.com/Documentation/Client/method/ExecuteCloudScript)
// 
// 2) You create PlayStream event actions that call them when a particular 
// event occurs, passing in the event and associated player profile data.
// (https://api.playfab.com/playstream/docs)
// 
// 3) For titles using the Photon Add-on (https://playfab.com/marketplace/photon/),
// Photon room events trigger webhooks which call corresponding Cloud Script functions.
// 
// The following examples demonstrate all three options.
//
///////////////////////////////////////////////////////////////////////////////////////////////////////
// This is a Cloud Script function. "args" is set to the value of the "FunctionParameter" 
// parameter of the ExecuteCloudScript API.
// (https://api.playfab.com/Documentation/Client/method/ExecuteCloudScript)
// "context" contains additional information when the Cloud Script function is called from a PlayStream action.
var HelloWorldDefault = function (args, context) {
    // The pre-defined "currentPlayerId" variable is initialized to the PlayFab ID of the player logged-in on the game client. 
    // Cloud Script handles authenticating the player automatically.
    var message = "Hello " + currentPlayerId + "!";
    // You can use the "log" object to write out debugging statements. It has
    // three functions corresponding to logging level: debug, info, and error. These functions
    // take a message string and an optional object.
    log.info(message);
    var inputValue = null;
    if (args && args.inputValue)
        inputValue = args.inputValue;
    log.debug("helloWorld:", { input: args.inputValue });
    // The value you return from a Cloud Script function is passed back 
    // to the game client in the ExecuteCloudScript API response, along with any log statements
    // and additional diagnostic information, such as any errors returned by API calls or external HTTP
    // requests. They are also included in the optional player_executed_cloudscript PlayStream event 
    // generated by the function execution.
    // (https://api.playfab.com/playstream/docs/PlayStreamEventModels/player/player_executed_cloudscript)
    return { messageValue: message };
};
handlers["helloWorld"] = HelloWorldDefault;
// This is a simple example of making a PlayFab server API call
var MakeApiCall = function (args, context) {
    var request = {
        PlayFabId: currentPlayerId, Statistics: [{
                StatisticName: "Level",
                Value: 2
            }]
    };
    // The pre-defined "server" object has functions corresponding to each PlayFab server API 
    // (https://api.playfab.com/Documentation/Server). It is automatically 
    // authenticated as your title and handles all communication with 
    // the PlayFab API, so you don't have to write extra code to issue HTTP requests. 
    var playerStatResult = server.UpdatePlayerStatistics(request);
};
handlers["makeAPICall"] = MakeApiCall;
// This is a simple example of a function that is called from a
// PlayStream event action. (https://playfab.com/introducing-playstream/)
var HandlePlayStreamEventAndProfile = function (args, context) {
    // The event that triggered the action 
    // (https://api.playfab.com/playstream/docs/PlayStreamEventModels)
    var psEvent = context.playStreamEvent;
    // The profile data of the player associated with the event
    // (https://api.playfab.com/playstream/docs/PlayStreamProfileModels)
    var profile = context.playerProfile;
    // Post data about the event to an external API
    var content = JSON.stringify({ user: profile.PlayerId, event: psEvent.EventName });
    var response = http.request('https://httpbin.org/status/200', 'post', content, 'application/json', null);
    return { externalAPIResponse: response };
};
handlers["handlePlayStreamEventAndProfile"] = HandlePlayStreamEventAndProfile;
// Below are some examples of using Cloud Script in slightly more realistic scenarios
// This is a function that the game client would call whenever a player completes
// a level. It updates a setting in the player's data that only game server
// code can write - it is read-only on the client - and it updates a player
// statistic that can be used for leaderboards. 
//
// A funtion like this could be extended to perform validation on the 
// level completion data to detect cheating. It could also do things like 
// award the player items from the game catalog based on their performance.
var CompletedLevel = function (args, context) {
    var level = args.levelName;
    var monstersKilled = args.monstersKilled;
    var updateUserDataResult = server.UpdateUserInternalData({
        PlayFabId: currentPlayerId,
        Data: {
            lastLevelCompleted: level
        }
    });
    log.debug("Set lastLevelCompleted for player " + currentPlayerId + " to " + level);
    var request = {
        PlayFabId: currentPlayerId, Statistics: [{
                StatisticName: "level_monster_kills",
                Value: monstersKilled
            }]
    };
    server.UpdatePlayerStatistics(request);
    log.debug("Updated level_monster_kills stat for player " + currentPlayerId + " to " + monstersKilled);
};
handlers["completedLevel"] = CompletedLevel;
var CreatePlaySpace = function (Level, context) {
    var data = {
        "Key": "test",
        "value": ["a", "b"]
    };
    var setTitleDataResponse = server.SetTitleData(data);
};
handlers["createPlaySpace"] = CreatePlaySpace;
function GetEntityToken(params, context) {
    var getTokenRequest = {};
    var getTokenResponse = entity.GetEntityToken(getTokenRequest);
    var entityId = getTokenResponse.Entity.Id;
    var entityType = getTokenResponse.Entity.Type;
}
handlers.GetEntityToken = GetEntityToken;
function GetObjects(params, context) {
    var getObjRequest = {
        Entity: {
            Id: params.entityId,
            Type: params.entityType
        }
    };
    var getObjResponse = entity.GetObjects(getObjRequest);
    var entityId = getObjResponse.Entity.Id;
    var entityType = getObjResponse.Entity.Type;
    var entityObjs = getObjResponse.Objects["testKey"];
}
handlers.GetObjects = GetObjects;