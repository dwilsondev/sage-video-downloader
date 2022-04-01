/*
if('serviceWorker' in navigator){
    navigator.serviceWorker.register('./serviceworker.js')
      .then(reg => console.log('service worker registered'))
      .catch(err => console.log('service worker not registered', err));
}
*/

function downloadVideo() {
    let url = document.querySelector('#url');
    let download_option = document.querySelector("#download_option");
    
    let download_link = document.querySelector('#download_link');
    let wait_message = document.querySelector("#wait_message");
    let error_message = document.querySelector("#error_message");
    let submit = document.querySelector('#submit');
    
    download_link.style.cssText = "display: none";
    error_message.style.cssText = "display: none";  

    if(url !== "" && download_option !== "") {
        let formData = new FormData(document.querySelector('#download_video'));
        formData.append('url', url.value);
        formData.append('download_option', download_option.value);

        //submit.style.cssText = "display: none";
        wait_message.style.cssText = "display: block";

        fetch("app/download.php", {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(result => {
                // Link means file was upload successfully, so
                // show download link.
                if(result['link']) {
                    download_link.href = result['link'];
                    download_link.style.cssText = "display: block";
                } else if(result['error']) {
                    // Errors were found, display error message.
                    error_message.innerHTML = result['error'];
                    error_message.style.cssText = "display: block";
                } else {
                    // Unknown error.
                    error_message.innerHTML = "There was a problem downloading file.";
                    error_message.style.cssText = "display: block";
                }

                // Show wait message, and show submit button.
                wait_message.style.cssText = "display: none";
                submit.style.cssText = "display: block";
            })
    }
}