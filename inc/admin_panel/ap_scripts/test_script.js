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

document.addEventListener('DOMContentLoaded',function (){

let imgLinks = ['gallery_']
    processedImgLink(imgLinks);

})