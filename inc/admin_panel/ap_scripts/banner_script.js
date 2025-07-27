function processedImgLink(imgNames) {
        function imgLinkUploadPhoto(imgName, data) {
            const previewContainer = document.querySelector(`#img_link_preview_container_${imgName}`);
            if (!previewContainer) return;
            // Очистити попередній вміст
            previewContainer.innerHTML = '';

            const attachments = Array.isArray(data) ? data : [data];

            attachments.forEach(attachment => {
                const imageWrapper = document.createElement('div');
                imageWrapper.className = 'img_link_preview_item';

                const btnRemove = document.createElement('input');
                btnRemove.type = 'button'
                btnRemove.value = 'x'
                btnRemove.classList.add('img_link_preview_btn')
                btnRemove.addEventListener('click', function (){
                    previewContainer.removeChild(imageWrapper);
                    imgLinkUpdateForRestApi(imgName)
                })

                const img = document.createElement('img');
                img.src = attachment


                imageWrapper.appendChild(img);
                imageWrapper.appendChild(btnRemove);
                previewContainer.appendChild(imageWrapper);
            });
            imgLinkUpdateForRestApi(imgName)
        }

        function imgLinkInitMediaUploader(imgName) {
            const uploadBtn = document.getElementById(`img_link_upload_${imgName}`);
            if (!uploadBtn) return;

            let mediaUploader;
            const multiple = imgName.endsWith('_');


            uploadBtn.addEventListener('click', function (e) {
                e.preventDefault();

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: 'Select or Upload Images',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: multiple
                });

                mediaUploader.on('select', function () {
                    const selection = mediaUploader.state().get('selection');
                    const attachments = selection.toArray();
                    let attachments_data = []
                    attachments.forEach( el =>{
                            attachments_data.push(el.attributes.url)
                        }

                    )
                    imgLinkUploadPhoto(imgName, attachments_data);

                });

                mediaUploader.open();
            });
        }

        function imgLinkUpdateForRestApi(imgName){
            const imgLinkData = document.querySelector(`#img_link_data_${imgName}`);
            const previewContainer = document.querySelectorAll(`#img_link_preview_container_${imgName} .img_link_preview_item`);
            let dataLink = []
            previewContainer.forEach( el =>{
                let link = el.querySelector('img').src
                dataLink.push(link)
            })
            imgLinkData.setAttribute('value',JSON.stringify(dataLink))
        }

        function imgLinkLoad(imgName){
            const imgLinkData = document.querySelector(`#img_link_data_${imgName}`);
            let imgLinkDataArray = [];
            try {
                imgLinkDataArray = JSON.parse(imgLinkData.value);
            } catch (e) {
                console.log('Помилка при парсингу JSON: перший запуск поста');
            }
            imgLinkUploadPhoto(imgName, imgLinkDataArray)
        }

    imgNames.forEach(imgName =>{

        imgLinkInitMediaUploader(imgName);
        imgLinkLoad(imgName)
    })


}

function processedVideoLink(videoNames) {
    function videoLinkUploadFile(videoName, data) {
        const previewContainer = document.querySelector(`#video_preview_container_${videoName}`);
        if (!previewContainer) return;

        // Очистити попередній вміст
        previewContainer.innerHTML = '';

        const attachments = Array.isArray(data) ? data : [data];

        attachments.forEach(attachment => {
            const videoWrapper = document.createElement('div');
            videoWrapper.className = 'video_preview_item';

            const btnRemove = document.createElement('input');
            btnRemove.type = 'button';
            btnRemove.value = 'x';
            btnRemove.classList.add('video_preview_btn');
            btnRemove.addEventListener('click', function () {
                previewContainer.removeChild(videoWrapper);
                videoLinkUpdateForRestApi(videoName);
            });

            const video = document.createElement('video');
            video.src = attachment;
            video.controls = true;
            video.width = 300;

            videoWrapper.appendChild(video);
            videoWrapper.appendChild(btnRemove);
            previewContainer.appendChild(videoWrapper);
        });

        videoLinkUpdateForRestApi(videoName);
    }

    function videoLinkInitMediaUploader(videoName) {
        const uploadBtn = document.getElementById(`video_upload_${videoName}`);
        if (!uploadBtn) return;

        let mediaUploader;
        const multiple = videoName.endsWith('_');

        uploadBtn.addEventListener('click', function (e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Select or Upload Videos',
                button: {
                    text: 'Use this video'
                },
                library: {
                    type: 'video'
                },
                multiple: multiple
            });

            mediaUploader.on('select', function () {
                const selection = mediaUploader.state().get('selection');
                const attachments = selection.toArray();
                let attachments_data = []
                attachments.forEach(el => {
                    attachments_data.push(el.attributes.url);
                });
                videoLinkUploadFile(videoName, attachments_data);
            });

            mediaUploader.open();
        });
    }

    function videoLinkUpdateForRestApi(videoName) {
        const videoLinkData = document.querySelector(`#video_data_${videoName}`);
        const previewItems = document.querySelectorAll(`#video_preview_container_${videoName} .video_preview_item`);
        let dataLink = [];
        previewItems.forEach(el => {
            const link = el.querySelector('video').src;
            dataLink.push(link);
        });
        videoLinkData.setAttribute('value', JSON.stringify(dataLink));
    }

    function videoLinkLoad(videoName) {
        const videoLinkData = document.querySelector(`#video_data_${videoName}`);
        let videoLinkDataArray = [];
        try {
            videoLinkDataArray = JSON.parse(videoLinkData.value);
        } catch (e) {
            console.log('Помилка при парсингу JSON: перший запуск поста');
        }
        videoLinkUploadFile(videoName, videoLinkDataArray);
    }

    videoNames.forEach(videoName => {
        videoLinkInitMediaUploader(videoName);
        videoLinkLoad(videoName);
    });
}

document.addEventListener('DOMContentLoaded',function (){

let imgLinks = ['aside_photo', 'banner']
    processedImgLink(imgLinks);

let videoLinks = ['aside_video'];
    processedVideoLink(videoLinks);

})