
//Download sermon
jQuery('.download-btn').on('click', function(e) {
    e.preventDefault(); 
    
    let track = jQuery(this).attr('href');
    track = track.substring(this.href.lastIndexOf('/') + 1);

    let hostName = window.location.hostname;

    if(hostName === 'localhost') {
        hostName = 'http://localhost/rickhoward'
    } else {
        hostName = window.location.hostname;
    }

    const url = `${hostName}/wp-json/download-sermon/v1/${track}`;

    console.log(url);

    fetch(url)
    .then((response) => response.blob())
    .then(blob => { 
        const url = window.URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = track;
        document.body.appendChild(a);
        a.click();  
        a.remove();
    });

})

