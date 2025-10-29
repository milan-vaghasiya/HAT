// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries
// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyC8qV_SjerPiNPCpo1M8Rv8D3YwnbJvZgQ",
    authDomain: "nativebit-175ba.firebaseapp.com",
    projectId: "nativebit-175ba",
    storageBucket: "nativebit-175ba.appspot.com",
    messagingSenderId: "695494499148",
    appId: "1:695494499148:web:778317ff0a732dfe7a0de9",
    measurementId: "G-SF9XGNSK36"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();
messaging.usePublicVapidKey("BMbDoUsYZnTpaZ5OnBWc-NlwsPk5wxBKXkKgAi8EnvQ2AtJpvap6OsW83n01g_kej2j1K8-cU0nEPhVLmrvl76Y");

// Get Instance ID token. Initially this makes a network call, once retrieved
// subsequent calls to getToken will return from cache.
messaging.getToken().then((currentToken) => {
    if (currentToken) {
        console.log('currentToken', currentToken);
        $("#loginform #web_token").val(currentToken);
    } else {
        // Show permission request.
        console.log('No Instance ID token available. Request permission to generate one.');
    }
}).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
});

// Handle incoming messages. Called when:
// - a message is received while the app has focus
// - the user clicks on an app notification created by a service worker
//   `messaging.setBackgroundMessageHandler` handler.
messaging.onMessage((response) => {
    response = JSON.parse(response.data.data);
    //console.log(response); 
    var notificationTitle = response.title;
    var notificationOptions = {
        body: response.message,
        icon: response.image
    };

    if(Notification.permission !== "granted") {
        Notification.requestPermission();
    }else{
        var notification = new Notification(notificationTitle, notificationOptions);
        notification.onclick = function () {
          window.open(response.payload.callBack);
        };
    }
});

