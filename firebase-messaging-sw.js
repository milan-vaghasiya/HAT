importScripts('https://www.gstatic.com/firebasejs/7.22.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.22.0/firebase-messaging.js');

// Initialize Firebase
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


// If you would like to customize notifications that are received in the
// background (Web app is closed or not in browser focus) then you should
// implement this optional method.
// [START background_handler]
messaging.setBackgroundMessageHandler(function (response) {
    //console.log('[firebase-messaging-sw.js] Received background message ', response);
	
    // Customize notification here
    response = JSON.parse(response.data.data);
    var notificationTitle = response.title;
    var notificationOptions = {
        body: response.message,
        icon: response.image,
        tag : response.payload.callBack
    };
    return self.registration.showNotification(notificationTitle,notificationOptions);  
});
// [END background_handler]

messaging.onBackgroundMessage((response) => {
    //console.log('Message received.onBackgroundMessage ', payload);
    response = JSON.parse(response.data.data);
    //console.log(response);
    var notificationTitle = response.title;
    var notificationOptions = {
        body: response.message,
        icon: response.image,
        tag : response.payload.callBack
    }; 
    return self.registration.showNotification(notificationTitle,notificationOptions);    
});

self.addEventListener('notificationclick', function(event) {
    var url = event.notification.tag;
    console.log('On notification click: ', event.notification.tag);
    event.notification.close();
  
    if(url != "user_visible_auto_notification"){
        // This looks to see if the current is already open and
        // focuses if it is
        event.waitUntil(clients.matchAll({
            type: "window"
        }).then(function(clientList) {
            for (var i = 0; i < clientList.length; i++) {
            var client = clientList[i];
            if (client.url == url && 'focus' in client)
                return client.focus();
            }
            if(clients.openWindow)
            return clients.openWindow(url);
        }));
    }else{
        return clients.openWindow('/');
    }    
});
