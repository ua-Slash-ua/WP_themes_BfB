function actionTab(tabs) {
    tabs.forEach(tabName => {
        document.getElementById(tabName).addEventListener('click', function () {
            document.querySelectorAll('.mtab_header_item').forEach(navEl => {
                navEl.classList.remove('tab_active')
            })
            document.querySelectorAll('.mtab_content_item').forEach(navEl => {
                navEl.classList.remove('content_active')
            })
            document.getElementById(tabName).classList.add('tab_active')
            document.getElementById(`content_${tabName}`).classList.add('content_active')

            if (window.leafletMap) {
                window.leafletMap.invalidateSize();
            }
        })
    })
}

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
            btnRemove.addEventListener('click', function () {
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
                attachments.forEach(el => {
                        attachments_data.push(el.attributes.url)
                    }
                )
                imgLinkUploadPhoto(imgName, attachments_data);

            });

            mediaUploader.open();
        });
    }

    function imgLinkUpdateForRestApi(imgName) {
        const imgLinkData = document.querySelector(`#img_link_data_${imgName}`);
        const previewContainer = document.querySelectorAll(`#img_link_preview_container_${imgName} .img_link_preview_item`);
        let dataLink = []
        previewContainer.forEach(el => {
            let link = el.querySelector('img').src
            dataLink.push(link)
        })
        imgLinkData.setAttribute('value', JSON.stringify(dataLink))
    }

    function imgLinkLoad(imgName) {
        const imgLinkData = document.querySelector(`#img_link_data_${imgName}`);
        let imgLinkDataArray = [];
        try {
            imgLinkDataArray = JSON.parse(imgLinkData.value);
        } catch (e) {
            console.log('Помилка при парсингу JSON: перший запуск поста');
        }
        imgLinkUploadPhoto(imgName, imgLinkDataArray)
    }

    imgNames.forEach(imgName => {

        imgLinkInitMediaUploader(imgName);
        imgLinkLoad(imgName)
    })


}

function processHardLabel(hlNames) {
    function trimArray(arr) {
        if (arr.length <= 2) {
            return [];
        }
        return arr.slice(1, -1);
    }

    function sliceAfterUnderscore(text, sliceIndex, delimiter = "_") {
        const parts = text.split(delimiter);
        if (parts.length <= sliceIndex) {
            return "";
        }
        return parts.slice(sliceIndex).join(delimiter);
    }

    function createInputBtn(btnClass, btnValue, btnFunc) {
        const btn = document.createElement('input')
        btn.type = 'button'
        btn.value = btnValue
        btn.classList.add(btnClass)
        btn.addEventListener('click', function () {
            btnFunc(this)
        })
        return btn;
    }

    function actionBtnRemove(element) {
        let hlName = sliceAfterUnderscore(element.parentElement.parentElement.parentElement.parentElement.id, 2, '-')
        const mainDiv = document.querySelector(`#container-hl-${hlName}`)

        element.parentElement.parentElement.remove()
        updateHLForRestApi(mainDiv, hlName)

    }

    function actionEdit(element) {
        let hlName = sliceAfterUnderscore(element.parentElement.parentElement.parentElement.parentElement.id, 2, '-')
        const mainDiv = document.querySelector(`#container-hl-${hlName}`)
        const contentElement = [...element.parentElement.parentElement.querySelector('div').children]
        contentElement.forEach(elContainer => {
            let id = elContainer.querySelector('span').getAttribute('data-type')
            const el = document.getElementById(id)
            if (elContainer.classList.contains('hl_content_text')) {
                let value = elContainer.querySelector('p').textContent
                if (id.startsWith('hl_input_checkbox_')) {
                    el.checked = value === 'on';
                } else {
                    el.value = value
                }
            } else if (elContainer.classList.contains('hl_content_img_svg')) {
                const svgPrev = elContainer.querySelector('.hl_img_link_preview_container')
                el.querySelector('textarea').value = svgPrev.innerHTML
                el.querySelector('div').innerHTML = svgPrev.innerHTML
            } else if (elContainer.classList.contains('hl_content_img_link') || elContainer.classList.contains('hl_content_video')) {
                el.querySelector('div').innerHTML = elContainer.querySelector('div').innerHTML
            }
        })
        actionBtnRemove(element)

    }

    function openMediaUploader(buttonElement, multiple = false, callback) {
        buttonElement.addEventListener('click', function () {
            const frame = wp.media({
                title: 'Оберіть зображення',
                button: {
                    text: 'Вибрати'
                },
                multiple: multiple
            });

            frame.on('select', function () {
                const selection = frame.state().get('selection');
                const images = [];

                selection.forEach(function (attachment) {
                    const item = attachment.toJSON();
                    images.push({
                        id: item.id,
                        url: item.url,
                        alt: item.alt,
                        title: item.title
                    });
                });

                callback(images);
            });

            frame.open();
        });
    }


    function renderImagesInContainer(containerDiv, images) {
        if (!containerDiv) {
            console.error('❌ containerDiv не знайдено!');
            return;
        }

        containerDiv.innerHTML = '';

        const imageArray = Array.isArray(images) ? images : [images];

        const wrapperDiv = document.createElement('div');
        wrapperDiv.classList.add('image-wrapper');

        imageArray.forEach(url => {
            const img = document.createElement('img');
            img.src = url;
            img.alt = 'image';
            img.classList.add('image-item');
            wrapperDiv.appendChild(img);
        });

        containerDiv.appendChild(wrapperDiv);
    }

    function openVideoUploader(buttonElement, multiple = false, callback) {
        buttonElement.addEventListener('click', function () {
            const frame = wp.media({
                title: 'Оберіть відео',
                button: {
                    text: 'Вибрати'
                },
                multiple: multiple,
                library: {
                    type: 'video'
                }
            });

            frame.on('select', function () {
                const selection = frame.state().get('selection');
                const videos = [];

                selection.forEach(function (attachment) {
                    const item = attachment.toJSON();
                    videos.push({
                        id: item.id,
                        url: item.url,
                        title: item.title
                    });
                });

                callback(videos);
            });

            frame.open();
        });
    }

    function renderVideosInContainer(containerDiv, videos) {
        if (!containerDiv) {
            console.error('❌ containerDiv не знайдено!');
            return;
        }

        containerDiv.innerHTML = '';

        const videoArray = Array.isArray(videos) ? videos : [videos];

        const wrapperDiv = document.createElement('div');
        wrapperDiv.classList.add('video-wrapper');

        videoArray.forEach(videoUrl => {
            const videoEl = document.createElement('video');
            videoEl.src = videoUrl;
            videoEl.controls = true;
            videoEl.classList.add('video-item');
            videoEl.style.maxWidth = '100%';
            wrapperDiv.appendChild(videoEl);
        });

        containerDiv.appendChild(wrapperDiv);
    }


    function renderSVGInElement(svgData, containerElement) {
        if (!containerElement) return;
        containerElement.innerHTML = svgData;
    }

    function bindTextareaToSVGPreview(textareaElement, previewContainer) {
        if (!textareaElement || !previewContainer) return;

        textareaElement.addEventListener('input', function () {
            const svgCode = textareaElement.value;
            renderSVGInElement(svgCode, previewContainer)
        });
    }

    function enableDragAndDrop(container, mainDiv, hlName) {
        if (!container || !container.classList.contains('container-hl-preview')) {
            console.error('Invalid container element');
            return;
        }

        let draggedElement = null;
        let placeholder = null;

        // Створюємо placeholder елемент
        function createPlaceholder() {
            const div = document.createElement('div');
            div.className = 'drag-placeholder';
            div.style.cssText = `
            height: 2px;
            background-color: #007cba;
            margin: 5px 0;
            border-radius: 1px;
            opacity: 0.8;
        `;
            return div;
        }

        // Додаємо draggable атрибут до всіх елементів
        function makeDraggable() {
            const items = container.querySelectorAll('.container-hl-preview-item');
            items.forEach(item => {
                item.draggable = true;
                item.style.cursor = 'move';
            });
        }

        // Обробник початку перетягування
        function handleDragStart(e) {
            draggedElement = e.target.closest('.container-hl-preview-item');
            if (!draggedElement) return;

            draggedElement.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', draggedElement.outerHTML);
        }

        // Обробник перетягування над елементом
        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';

            const afterElement = getDragAfterElement(container, e.clientY);
            const items = container.querySelectorAll('.container-hl-preview-item');

            // Видаляємо попередній placeholder
            if (placeholder && placeholder.parentNode) {
                placeholder.parentNode.removeChild(placeholder);
            }

            placeholder = createPlaceholder();

            if (afterElement == null) {
                container.appendChild(placeholder);
            } else {
                container.insertBefore(placeholder, afterElement);
            }
        }

        // Визначаємо елемент, після якого треба вставити
        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.container-hl-preview-item:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return {offset: offset, element: child};
                } else {
                    return closest;
                }
            }, {offset: Number.NEGATIVE_INFINITY}).element;
        }

        // Обробник відпускання елемента
        function handleDrop(e) {
            e.preventDefault();

            if (!draggedElement || !placeholder) return;

            // Вставляємо елемент на місце placeholder
            if (placeholder.parentNode) {
                placeholder.parentNode.insertBefore(draggedElement, placeholder);
                placeholder.parentNode.removeChild(placeholder);
            }

            draggedElement.style.opacity = '1';
            draggedElement = null;
            placeholder = null;
        }

        // Обробник завершення перетягування
        function handleDragEnd(e) {
            const item = e.target.closest('.container-hl-preview-item');
            if (item) {
                item.style.opacity = '1';
            }

            // Видаляємо placeholder якщо він залишився
            if (placeholder && placeholder.parentNode) {
                placeholder.parentNode.removeChild(placeholder);
                placeholder = null;
            }

            draggedElement = null;
            updateHLForRestApi(mainDiv, hlName)
        }

        // Обробник входу в зону перетягування
        function handleDragEnter(e) {
            e.preventDefault();
        }

        // Обробник виходу з зони перетягування
        function handleDragLeave(e) {
            // Перевіряємо чи курсор справді покинув контейнер
            const rect = container.getBoundingClientRect();
            if (e.clientX < rect.left || e.clientX > rect.right ||
                e.clientY < rect.top || e.clientY > rect.bottom) {

                if (placeholder && placeholder.parentNode) {
                    placeholder.parentNode.removeChild(placeholder);
                    placeholder = null;
                }
            }
        }

        // Ініціалізація
        makeDraggable();

        // Додаємо event listeners
        container.addEventListener('dragstart', handleDragStart);
        container.addEventListener('dragover', handleDragOver);
        container.addEventListener('drop', handleDrop);
        container.addEventListener('dragend', handleDragEnd);
        container.addEventListener('dragenter', handleDragEnter);
        container.addEventListener('dragleave', handleDragLeave);

        // Функція для оновлення при додаванні нових елементів
        function updateDraggable() {
            makeDraggable();
        }

        // Повертаємо об'єкт з методами для керування
        return {
            updateDraggable: updateDraggable,
            destroy: function () {
                container.removeEventListener('dragstart', handleDragStart);
                container.removeEventListener('dragover', handleDragOver);
                container.removeEventListener('drop', handleDrop);
                container.removeEventListener('dragend', handleDragEnd);
                container.removeEventListener('dragenter', handleDragEnter);
                container.removeEventListener('dragleave', handleDragLeave);

                const items = container.querySelectorAll('.container-hl-preview-item');
                items.forEach(item => {
                    item.draggable = false;
                    item.style.cursor = '';
                });
            }

        };
    }


    function updateHLForRestApi(mainDiv, hlName) {
        const dataStr = document.querySelector(`#hl_data_${hlName}`);
        let data = [];

        const dataPreviewAll = [...mainDiv.querySelector('.container-hl-preview').children];

        dataPreviewAll.forEach(previewItem => {
            let dataItem = {};
            const contentBlock = previewItem.querySelector('.hl-preview-item-content');
            if (!contentBlock) return;

            const elementItems = [...contentBlock.children];

            elementItems.forEach(elementItem => {

                if (elementItem.classList.contains('hl_content_text')) {
                    const typeEl = elementItem.children[0];
                    const valueEl = elementItem.children[1];

                    if (!typeEl || !valueEl) return;

                    const key = typeEl.getAttribute('data-type');
                    const value = valueEl.textContent;

                    if (key) {
                        dataItem[key] = value;
                    }
                } else if (elementItem.classList.contains('hl_content_img_link')) {
                    const typeEl = elementItem.children[0];

                    if (!typeEl) return;

                    const key = typeEl.getAttribute('data-type');
                    let value = []
                    const allSrc = elementItem.querySelectorAll('img')
                    allSrc.forEach(element => {
                        value.push(element.src)
                    })
                    if (key) {
                        dataItem[key] = value;
                    }
                } else if (elementItem.classList.contains('hl_content_img_svg')) {
                    const typeEl = elementItem.children[0];

                    if (!typeEl) return;

                    const key = typeEl.getAttribute('data-type');
                    let value = elementItem.children[1].innerHTML
                    if (key) {
                        dataItem[key] = value;
                    }
                } else if (elementItem.classList.contains('hl_content_video')) {
                    const typeEl = elementItem.children[0];

                    if (!typeEl) return;

                    const key = typeEl.getAttribute('data-type');
                    let value = []
                    const allSrc = elementItem.querySelectorAll('video')
                    allSrc.forEach(element => {
                        value.push(element.src)
                    })
                    if (key) {
                        dataItem[key] = value;

                    }
                }

            });
            data.push(dataItem);
        });

        const dataJson = JSON.stringify(data);
        if (dataStr) {
            dataStr.setAttribute('value', dataJson)
            dataStr.value = dataJson; // достатньо цього
        }
    }

    function getHl(mainDiv, hlName) {
        let allElements = trimArray([...mainDiv.querySelector('.container-hl-add').children]);
        let dataValue = {}
        allElements.forEach(element => {
            if (element.id.startsWith('hl_input') || element.id.startsWith('hl_textarea')) {
                if (element.id.startsWith('hl_input_checkbox')) {
                    if (element.checked) {
                        dataValue[element.id] = 'on'
                    } else {
                        dataValue[element.id] = 'off'
                    }
                    element.checked = false
                } else {
                    dataValue[element.id] = element.value
                    element.value = ''
                }
            } else if (element.id.startsWith('hl_img_link_')) {
                let dataImg = []
                const allSrc = element.querySelectorAll('img')
                allSrc.forEach(element => {
                    dataImg.push(element.src)
                })
                dataValue[element.id] = dataImg
                element.querySelector('div').innerHTML = ''
            } else if (element.id.startsWith('hl_img_svg_')) {
                dataValue[element.id] = element.querySelector('div').innerHTML
                element.querySelector('div').innerHTML = ''
                element.querySelector('textarea').value = ''
            } else if (element.id.startsWith('hl_video_')) {
                let dataImg = []
                const allSrc = element.querySelectorAll('video')
                allSrc.forEach(element => {
                    dataImg.push(element.src)
                })
                dataValue[element.id] = dataImg
                element.querySelector('div').innerHTML = ''
            }


        })
        return dataValue;
    }

    function createElement(mainDiv, data, hlName) {
        if (!Array.isArray(data)) {
            data = [data];
        }
        console.log(data)
        const previewDiv = mainDiv.querySelector('.container-hl-preview')
        data.forEach(elementBlock => {
            console.log(elementBlock)
            const divItem = document.createElement('div')
            divItem.classList.add('container-hl-preview-item')
            const divItemContent = document.createElement('div')
            divItemContent.classList.add('hl-preview-item-content')
            for (const elementId in elementBlock) {
                const elementValue = elementBlock[elementId]
                const divContentItem = document.createElement('div')
                if (elementId.startsWith('hl_input') || elementId.startsWith('hl_textarea')) {
                    divContentItem.classList.add('hl_content_text')
                    const spanName = document.createElement('span')
                    if (elementId.startsWith('hl_textarea')) {
                        spanName.textContent = sliceAfterUnderscore(elementId, 2)
                    } else {
                        spanName.textContent = sliceAfterUnderscore(elementId, 3)
                    }
                    spanName.setAttribute('data-type', elementId)
                    const pText = document.createElement('p')

                    pText.textContent = elementValue

                    divContentItem.appendChild(spanName)
                    divContentItem.appendChild(pText)
                    if (elementId.startsWith('hl_input_color')) {
                        const divColor = document.createElement('div')
                        divColor.classList.add('hl_input_color_preview')
                        divColor.style.backgroundColor = elementValue
                        divContentItem.appendChild(divColor)
                    }
                } else if (elementId.startsWith('hl_img_link_')) {

                    divContentItem.classList.add('hl_content_img_link')
                    const spanName = document.createElement('span')
                    spanName.setAttribute('data-type', elementId)
                    spanName.textContent = sliceAfterUnderscore(elementId, 3)
                    const divImgContainer = document.createElement('div')
                    divImgContainer.classList.add('hl_img_link_preview_container')
                    divContentItem.appendChild(spanName)
                    divContentItem.appendChild(divImgContainer)
                    renderImagesInContainer(divImgContainer, elementValue)

                } else if (elementId.startsWith('hl_img_svg_')) {

                    divContentItem.classList.add('hl_content_img_svg')
                    const spanName = document.createElement('span')
                    spanName.setAttribute('data-type', elementId)
                    spanName.textContent = sliceAfterUnderscore(elementId, 3)
                    const divImgContainer = document.createElement('div')
                    divImgContainer.classList.add('hl_img_link_preview_container')
                    divImgContainer.innerHTML = elementValue
                    divContentItem.appendChild(spanName)
                    divContentItem.appendChild(divImgContainer)
                } else if (elementId.startsWith('hl_video')) {
                    divContentItem.classList.add('hl_content_video')
                    const spanName = document.createElement('span')
                    spanName.setAttribute('data-type', elementId)
                    spanName.textContent = sliceAfterUnderscore(elementId, 2)
                    const divVideoContainer = document.createElement('div')
                    divVideoContainer.classList.add('hl_video_preview_container')
                    divContentItem.appendChild(spanName)
                    divContentItem.appendChild(divVideoContainer)
                    renderVideosInContainer(divVideoContainer, elementValue)

                }


                divItemContent.appendChild(divContentItem)

            }
            const divItemAction = document.createElement('div')
            divItemAction.classList.add('hl-preview-item-action')

            const btnRemove = createInputBtn("hl_btn_remove", 'X', actionBtnRemove)
            const btnEdit = createInputBtn("hl_btn_edit", 'i', actionEdit)
            divItemAction.appendChild(btnRemove)
            divItemAction.appendChild(btnEdit)

            divItem.appendChild(divItemContent)
            divItem.appendChild(divItemAction)
            previewDiv.appendChild(divItem)
        })

        updateHLForRestApi(mainDiv, hlName)
        const dragAndDrop = enableDragAndDrop(previewDiv, mainDiv, hlName);
        dragAndDrop.updateDraggable();
    }

    hlNames.forEach(hlName => {
        const btnAdd = document.querySelector(`#hl_btn_add_${hlName}`)
        const mainDiv = document.querySelector(`#container-hl-${hlName}`)
        const dataStr = document.querySelector(`#hl_data_${hlName}`);
        const allBtnUploadPhoto = document.querySelectorAll('[id^="hl_img_link_upload_"]');
        const allBtnUploadVideo = document.querySelectorAll('[id^="hl_video_upload_"]');
        const allSvgInput = document.querySelectorAll('[id^="hl_img_svg_input_"]');
        allBtnUploadPhoto.forEach(btnElement => {
            let btnName = sliceAfterUnderscore(btnElement.id, 4)
            let multiple = false
            if (btnName.endsWith('_')) {
                multiple = true
            }
            const previewContainer = document.querySelector(`#hl_img_link_preview_container_${btnName}`)
            openMediaUploader(btnElement, multiple, function (images) {
                renderImagesInContainer(previewContainer, images.map(img => img.url),)
            })
        })
        allBtnUploadVideo.forEach(btnElement => {
            let btnName = sliceAfterUnderscore(btnElement.id, 3);
            let multiple = false;

            if (btnName.endsWith('_')) {
                multiple = true;
            }
            const previewContainer = document.querySelector(`#hl_video_preview_container_${btnName}`);

            openVideoUploader(btnElement, multiple, function (videos) {
                // Передаємо масив об'єктів відео, як є
                renderVideosInContainer(previewContainer, videos.map(video => video.url));
            });
        });
        const dragAndDrop = enableDragAndDrop(mainDiv.querySelector('.container-hl-preview'), mainDiv, hlName);

        allSvgInput.forEach(textarea => {
            const previewSvg = textarea.parentElement.querySelector('div')
            bindTextareaToSVGPreview(textarea, previewSvg)
        })

        let dataArray = [];
        if (dataStr) {
            try {
                dataArray = JSON.parse(dataStr.value);
            } catch (e) {
                console.error('JSON parsing error:', e);
                dataArray = [];
            }
        } else {
            console.warn(`#hl_data_${hlName} not found`);
        }

        console.log(dataArray)
        createElement(mainDiv, dataArray, hlName)
        btnAdd.addEventListener('click', function () {
            let data = getHl(mainDiv, hlName)

            createElement(mainDiv, data, hlName)
        })
    })

}

function processPoint(pointNames) {

    function enableDragAndDrop(container, pointName) {
        let draggedItem = null;

        // Add event listeners to all existing items in the container
        container.querySelectorAll('.point_item').forEach(item => {
            item.setAttribute('draggable', 'true');
            addDragEvents(item);
        });

        function addDragEvents(item) {
            // When drag starts
            item.addEventListener('dragstart', function (e) {
                draggedItem = item;
                setTimeout(() => {
                    item.classList.add('dragging');
                }, 0);
            });

            // When drag ends
            item.addEventListener('dragend', function () {
                item.classList.remove('dragging');
                draggedItem = null;
                // Update data after dragging is complete
                pointUpdateForRestApi(pointName);
            });

            // Prevent default behaviors for some events
            item.addEventListener('dragover', function (e) {
                e.preventDefault();
            });

            item.addEventListener('dragenter', function (e) {
                e.preventDefault();
                if (this !== draggedItem) {
                    this.classList.add('drag-over');
                }
            });

            item.addEventListener('dragleave', function () {
                this.classList.remove('drag-over');
            });

            // Handle dropping
            item.addEventListener('drop', function (e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                if (draggedItem && this !== draggedItem) {
                    // Get positions to determine order
                    const thisRect = this.getBoundingClientRect();
                    const draggedRect = draggedItem.getBoundingClientRect();

                    // Determine if dragged item should be before or after this item
                    if (draggedRect.top < thisRect.top) {
                        container.insertBefore(draggedItem, this);
                    } else {
                        container.insertBefore(draggedItem, this.nextSibling);
                    }
                }
            });
        }

        // Container level events
        container.addEventListener('dragover', function (e) {
            e.preventDefault();
            // Only proceed if we have a valid draggedItem
            if (!draggedItem) return;

            const afterElement = getDragAfterElement(container, e.clientY);
            if (afterElement === null) {
                // Only append if draggedItem exists
                container.appendChild(draggedItem);
            } else if (afterElement !== draggedItem) {
                container.insertBefore(draggedItem, afterElement);
            }
        });

        container.addEventListener('drop', function (e) {
            e.preventDefault();
            // Update after drop
            pointUpdateForRestApi(pointName);
        });

        function getDragAfterElement(container, y) {
            // Convert NodeList to Array and filter out the currently dragged element
            const draggableElements = [...container.querySelectorAll('.point_item:not(.dragging)')];

            // If no elements, return null
            if (draggableElements.length === 0) return null;

            // Find the closest element after cursor position
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return {offset: offset, element: child};
                } else {
                    return closest;
                }
            }, {offset: Number.NEGATIVE_INFINITY}).element;
        }
    }

    function pointAdd(pointName, pointValue) {

        const pointItemMain = document.getElementById(`point_container_${pointName}`)

        const pointItemDiv = document.createElement('div')
        pointItemDiv.classList.add('point_item')


        const pointItemSpan = document.createElement('span')
        pointItemSpan.textContent = pointValue

        const pointItemBtn = document.createElement('input')
        pointItemBtn.type = 'button'
        pointItemBtn.value = 'x'
        pointItemBtn.classList.add('point_del')
        pointItemBtn.addEventListener('click', function () {
            pointItemMain.removeChild(pointItemDiv)
            pointUpdateForRestApi(pointName)
        })

        pointItemDiv.appendChild(pointItemSpan)
        pointItemDiv.appendChild(pointItemBtn)
        pointItemMain.appendChild(pointItemDiv)
        pointUpdateForRestApi(pointName)
        enableDragAndDrop(pointItemMain, pointName)
    }

    function pointUpdateForRestApi(pointName) {
        const pointItemMain = document.getElementById(`point_container_${pointName}`)
        const pointData = document.getElementById(`point_data_${pointName}`)
        let pointDataArray = []

        pointItemMain.querySelectorAll('.point_item').forEach(item => {
            const pointValue = item.querySelector('span').textContent.trim()
            pointDataArray.push(pointValue)
        })

        pointData.setAttribute('value', JSON.stringify(pointDataArray))

    }

    function pointLoad(pointName) {
        const pointData = document.getElementById(`point_data_${pointName}`);
        let pointDataArray = [];

        try {
            const parsed = JSON.parse(pointData.value);
            if (Array.isArray(parsed)) {
                pointDataArray = parsed;
            }
        } catch (e) {
            console.warn(`Не вдалося розпарсити дані для ${pointName}:`, e);
        }

        pointDataArray.forEach(pointValue => {
            pointAdd(pointName, pointValue);
        });
    }

    pointNames.forEach(pointName => {
        pointLoad(pointName)
        document.getElementById(`point_add_${pointName}`).addEventListener('click', function () {
            const pointValue = document.getElementById(`point_input_${pointName}`)

            if (!pointValue.value.trim()) {
                // Додаємо клас одразу
                pointValue.classList.add('error');

                // А потім через деякий час — прибираємо, щоб ефект був тимчасовий
                setTimeout(function () {
                    pointValue.classList.remove('error');
                }, 1500); // наприклад, через 0.8 сек
            } else {
                pointAdd(pointName, pointValue.value.trim())
                pointValue.value = ''

            }

        })
    })


}

document.addEventListener('DOMContentLoaded', function () {
    let tabs = ['self', 'specialty', 'exercise', 'experience', 'wlocation', 'certificate', 'gallery']
    actionTab(tabs)

    let points = ['favourite_exercise', 'my_specialty']
    processPoint(points)

    let hlNames = [
        'my_experience',
        'my_wlocation']
    processHardLabel(hlNames)
    let imgLinks = ['gallery_']
    processedImgLink(imgLinks);

})