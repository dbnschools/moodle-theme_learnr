window.addEventListener('load', function () {
    // get the progressbar
    var pr = document.getElementsByClassName('progress')[0];
    var prbar = document.getElementsByClassName('progress-bar progress-bar-info')[0];
    console.log('pr' + pr);
    console.log('prbar' + prbar);
})

// https://riptutorial.com/javascript/example/20197/listening-to-ajax-events-at-a-global-level
// Store a reference to the native method
let send = XMLHttpRequest.prototype.send;

// Overwrite the native method
XMLHttpRequest.prototype.send = function() {
    // tinjohn get Request Payload from send arguments
    payload = arguments[0];
    console.log("arguments 0 send-----" + payload);
    var isValidJSON = true;
    try { JSON.parse(payload) } catch { isValidJSON = false }
    if(isValidJSON) {
        // Assign an event listener
        // and remove it again
        // https://www.mediaevent.de/javascript/remove-event-listener.html
        this.addEventListener("load", function removeMe () { 
            readAJAXrequestsend(payload);
            this.removeEventListener("load", removeMe);
        }, false);
    }
    // Call the stored reference to the native method
    send.apply(this, arguments);
};

function readAJAXrequestsend (payload) {
    console.log("readAJAXrequestsend-----payload---------" + payload);
    var isValidJSON = true;
    try { JSON.parse(payload) } catch { isValidJSON = false }
    if(isValidJSON) {
        const plo = JSON.parse(payload);
        /*    
        console.log(plo[0].methodname);
        console.log(plo[0].args.cmid);
        console.log(plo[0].args.completed);
        */
        // update_activity_completion_status_manually in completion/external.php
        if (plo[0].methodname.match("(.*)core_completion_update_activity_completion_status_manually(.*)")) 
        {
            var prbar = document.getElementsByClassName('progress-bar progress-bar-info')[0];
            if (plo[0].args.completed) {
                prbar.style.width = (parseInt(prbar.style.width) + parseInt(prbar.getAttribute('progress-steps'))) + '%';              
            } else {
                prbar.style.width = (parseInt(prbar.style.width) - parseInt(prbar.getAttribute('progress-steps'))) + '%';      
            }
        }
        payload = '';
    }
}


