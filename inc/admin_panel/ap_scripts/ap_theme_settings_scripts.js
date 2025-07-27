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

let map;
let editMode = false;
let markers = {
    "gym": [],

};
let markersGroup = null
const markerTypes = {
    gym: {
        label: 'Головні зали BFB',
        svg: `<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="56" height="56" rx="28" fill="#8426D7"/>
<path d="M39.999 23.5905C39.999 22.9971 39.7679 22.4391 39.3482 22.0196L38.2347 20.9061L38.8019 20.3389C38.8051 20.3358 38.8083 20.3327 38.8115 20.3295C39.6776 19.4634 39.6776 18.054 38.8115 17.1876C38.3919 16.768 37.8339 16.5369 37.2405 16.5369C36.6472 16.5369 36.0892 16.768 35.6695 17.1875C35.666 17.191 35.6624 17.1947 35.6589 17.1983L35.0929 17.7642L33.9794 16.6507C33.5598 16.2311 33.002 16 32.4085 16C31.815 16 31.2571 16.2311 30.8376 16.6508C30.6174 16.871 30.4535 17.1264 30.3453 17.399C30 17.1872 29.6024 17.0738 29.1871 17.0738C28.5937 17.0738 28.0357 17.3049 27.6159 17.7246C26.75 18.5908 26.75 20.0002 27.616 20.8665L29.8033 23.0537L23.0525 29.8045L20.8652 27.6172C20.4456 27.1976 19.8877 26.9665 19.2944 26.9665C18.701 26.9665 18.143 27.1976 17.7231 27.6174C16.9844 28.3562 16.8763 29.49 17.3979 30.3451C17.121 30.4554 16.8666 30.6215 16.6495 30.8387C15.7835 31.7048 15.7835 33.1142 16.6496 33.9805L17.763 35.0939L17.1864 35.6704C16.7668 36.0901 16.5357 36.648 16.5357 37.2414C16.5357 37.8348 16.7668 38.3927 17.1864 38.8122C17.606 39.2319 18.1639 39.463 18.7574 39.463C19.3455 39.463 19.8987 39.236 20.3169 38.8236C20.3208 38.8199 20.3247 38.8161 20.3285 38.8123L20.9049 38.2358L22.0184 39.3493C22.4379 39.7689 22.9958 39.9999 23.5893 40H23.5894C24.1829 40 24.7409 39.7688 25.1606 39.3491C25.3807 39.129 25.5446 38.8736 25.6528 38.6012C25.9979 38.8127 26.3955 38.9262 26.8106 38.9262H26.8107C27.4041 38.9262 27.9621 38.6951 28.3818 38.2755C28.8014 37.8558 29.0324 37.2979 29.0324 36.7045C29.0324 36.111 28.8014 35.5531 28.3818 35.1336L26.1944 32.9462L32.9454 26.1955L35.1327 28.3826C35.5523 28.8022 36.1102 29.0333 36.7036 29.0333H36.7038C37.297 29.0333 37.855 28.8022 38.2745 28.3827C38.6943 27.9631 38.9254 27.4052 38.9255 26.8117C38.9255 26.3968 38.8122 25.9996 38.6008 25.6545C38.8773 25.5442 39.1314 25.3782 39.3484 25.1613C39.7679 24.7418 39.999 24.1839 39.999 23.5905ZM19.3436 37.8084C19.3403 37.8115 19.3371 37.8148 19.3338 37.818C19.1799 37.972 18.9752 38.0568 18.7574 38.0568C18.5396 38.0568 18.3348 37.972 18.1807 37.8179C18.0268 37.6639 17.9419 37.4592 17.9419 37.2414C17.9419 37.0236 18.0268 36.8188 18.1808 36.6648L18.7573 36.0883L19.9105 37.2415L19.3436 37.8084ZM36.0873 18.7586L36.6634 18.1826C36.6661 18.1799 36.6688 18.1772 36.6714 18.1745C36.8247 18.0252 37.0263 17.9431 37.2406 17.9431C37.4584 17.9431 37.6632 18.028 37.8171 18.1818C38.1328 18.4977 38.1351 19.0101 37.8238 19.3285C37.8213 19.3308 37.8189 19.3332 37.8165 19.3356L37.2404 19.9117L36.6639 19.3352L36.0873 18.7586ZM31.832 17.645C31.9859 17.491 32.1907 17.4062 32.4085 17.4062C32.6264 17.4062 32.8311 17.491 32.9851 17.6451L35.6695 20.3296L36.7432 21.4033C36.7433 21.4034 36.7434 21.4035 36.7436 21.4036L38.354 23.014C38.5079 23.168 38.5928 23.3727 38.5928 23.5905C38.5928 23.8082 38.5079 24.0131 38.354 24.167C38.1999 24.3211 37.9951 24.4059 37.7773 24.4059C37.5599 24.4059 37.3555 24.3213 37.2016 24.1678L31.8308 18.7972C31.5141 18.4792 31.5144 17.9627 31.832 17.645ZM24.1662 38.3548C24.0121 38.5088 23.8072 38.5937 23.5893 38.5937C23.3715 38.5937 23.1667 38.5089 23.0128 38.355L21.4024 36.7446C21.4023 36.7445 21.4022 36.7443 21.4021 36.7443L21.0604 36.4026L17.644 32.9862C17.3262 32.6682 17.3261 32.151 17.644 31.833C17.7981 31.6789 18.0028 31.594 18.2206 31.594C18.4381 31.594 18.6426 31.6787 18.7965 31.8324L24.1691 37.205C24.4837 37.5232 24.4829 38.038 24.1662 38.3548ZM27.6261 36.7045C27.6261 36.9223 27.5413 37.1272 27.3874 37.2811C27.2333 37.4351 27.0285 37.52 26.8106 37.52C26.8106 37.52 26.8106 37.52 26.8106 37.52C26.5927 37.52 26.3879 37.4351 26.2339 37.2812L25.1665 36.2137C25.1644 36.2117 25.1625 36.2095 25.1605 36.2074L19.7915 30.8386L19.7913 30.8385L18.7176 29.7648C18.3997 29.4469 18.3997 28.9297 18.7176 28.6118C18.8717 28.4577 19.0766 28.3728 19.2944 28.3728C19.5122 28.3728 19.7169 28.4576 19.8708 28.6115L22.5549 31.2955C22.555 31.2957 22.5552 31.2959 22.5554 31.2961L24.5686 33.3092L27.3875 36.128C27.5414 36.282 27.6261 36.4867 27.6261 36.7045ZM25.2 31.9519L24.6234 31.3754L24.0469 30.7988L30.7977 24.048L31.951 25.2012L25.2 31.9519ZM37.2801 27.3882C37.1261 27.5423 36.9214 27.6271 36.7037 27.6271C36.4859 27.6271 36.281 27.5423 36.1269 27.3883L33.4443 24.7058C33.4437 24.7052 33.4432 24.7045 33.4426 24.7039L31.2949 22.5564C31.2946 22.556 31.2942 22.5557 31.2939 22.5554L28.6105 19.8722C28.2927 19.5542 28.2927 19.0367 28.6104 18.7188C28.7645 18.5648 28.9693 18.4799 29.1872 18.4799C29.405 18.4799 29.6098 18.5648 29.7638 18.7188L30.8345 19.7894C30.8355 19.7905 30.8364 19.7916 30.8375 19.7926L36.2065 25.1614C36.207 25.162 36.2075 25.1624 36.208 25.1628L37.2802 26.2351C37.4343 26.3891 37.5192 26.5938 37.5192 26.8117C37.5191 27.0295 37.4343 27.2342 37.2801 27.3882Z" fill="#FFFEFC"/>
</svg>
`
    },

};
let currentLat = null;
let currentLng = null;
let currentEditingMarker = null;

function alertMessage(msg, sts = 'info', time = 5) {
    let status = {
        "success": {
            'class': 'alert-status-success',
            'svgIcon': `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="green" viewBox="0 0 24 24">
                    <path d="M9 17l-5-5 1.41-1.41L9 14.17l9.59-9.59L20 6l-11 11z"/>
                </svg>`
        },
        "info": {
            'class': 'alert-status-info',
            'svgIcon': `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="blue" viewBox="0 0 24 24">
                 <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
             </svg>`
        },
        "error": {
            'class': 'alert-status-error',
            'svgIcon': `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="red" viewBox="0 0 24 24">
                 <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
             </svg>`
        },

    }
    let svgIcon = status[sts]['svgIcon']
    let cls = status[sts]['class']

    function showAlert(f_msg, f_sts, svgIcon, cls) {
        const alertContainer = document.querySelector('.message_alert');
        const alertContainerIcon = alertContainer.querySelector('.msg-icon');
        const alertContainerText = alertContainer.querySelector('p');
        const alertContainerH3 = alertContainer.querySelector('h3');
        const progressBar = alertContainer.querySelector('.alert-progress-bar');

        alertContainerIcon.innerHTML = svgIcon;
        alertContainerText.textContent = f_msg;
        alertContainerH3.textContent = `Application повідомляє < ${f_sts} >`;
        alertContainer.classList.add(cls);

        // Початкове значення прогрес-бару
        progressBar.style.width = '100%';
        progressBar.style.transition = `width ${time}s linear`;

        // Запускаємо плавне заповнення
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 100); // Коротка затримка, щоб `transition` коректно відпрацював

        setTimeout(() => {
            Object.keys(status).forEach(key => {
                let s = status[key]['class'];
                if (alertContainer.classList.contains(s)) {
                    alertContainer.classList.remove(s);
                }
            });

            // Після завершення повзунок плавно зменшується назад до 0%
            progressBar.style.width = '0%';

        }, time * 1075);
    }


    showAlert(msg, sts, svgIcon, cls)

}

function processMap() {

    function saveForRestApi() {
        const inputData = document.getElementById('map_markers')
        inputData.value = JSON.stringify(markers)
        inputData.setAttribute('value', JSON.stringify(markers))
    }

    function removeCoordinates(targetCoords, targetType = null) {
        const [targetLat, targetLng] = targetCoords.map(c => parseFloat(c).toFixed(6));

        for (const key in markers) {
            if (markers.hasOwnProperty(key)) {
                // Якщо задано тип — чистимо лише в ньому
                if (targetType && key !== targetType) continue;

                markers[key] = markers[key].filter(coord => {
                    const [lat, lng] = coord.map(c => parseFloat(c).toFixed(6));
                    return !(lat === targetLat && lng === targetLng);
                });
            }
        }
    }


    function openModal() {
        document.querySelector('.pop-up-marker').style.display = 'block'
        document.querySelector('.pop-up-overlay').style.display = 'block'
    }

    function closeModal() {
        document.querySelector('.pop-up-marker').style.display = 'none'
        document.querySelector('.pop-up-overlay').style.display = 'none'
    }

    function renderMapWithMarkers(containerId = 'main_map', center = [49.574507, 31.503004], zoom = 6) {
        // Очищуємо попередню карту, якщо є
        if (window.leafletMap) {
            window.leafletMap.remove();
        }

        // Ініціалізуємо нову карту
        map = L.map(containerId).setView(center, zoom);
        window.leafletMap = map;
        markersGroup = L.layerGroup().addTo(map);
        // Додаємо тайли
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);


    }

    function allowEdit() {
        const btnAllow = document.getElementById('editMarkersBtn')
        btnAllow.addEventListener('click', function () {
            editMode = !editMode
            this.value = editMode ? 'Зберегти мітки' : 'Редагувати мітки'
            let message = editMode ? 'Розпочато редагування міток' : 'Редагування міток завершено'
            alertMessage(message)
            saveForRestApi()
        })
    }

    function initLoad() {
        const inputData = document.getElementById('map_markers')
        let data = {}
        try {
            data = JSON.parse(inputData.value)
        } catch (e) {
            data = markers
        }
        for (let type in data) {
            data[type].forEach(coords => {
                let [lat, lng] = coords
                const marker = addMarker(lat, lng, type);
                if (marker) {
                    listenMarker(marker)
                }
            })

        }


    }

    function listenMap() {
        const saveBtn = document.getElementById('saveMarker');
        const closeBtn = document.querySelector('.modal-close');
        document.querySelector('.pop-up-header').querySelector('p').textContent = 'Додавання мітки'
        // Додаємо один обробник при ініціалізації
        saveBtn.addEventListener('click', function () {

            if (currentLat !== null && currentLng !== null) {
                const marker = addMarker(currentLat, currentLng);
                if (marker) {
                    listenMarker(marker)
                    closeModal();
                }
            }
        });

        closeBtn.addEventListener('click', closeModal);

        map.on('click', function (e) {
            if (editMode) {
                currentLat = e.latlng.lat;
                currentLng = e.latlng.lng;
                document.getElementById('marker_type_select').value = '';
                openModal();
                document.getElementById('editMarker').style.display = 'none';
                document.getElementById('saveMarker').style.display = 'block';
                document.getElementById('removeMarker').style.display = 'none';
            }
        });
    }

    function listenMarker(marker) {
        marker.on('click', function (e) {
            if (editMode) {
                currentEditingMarker = marker;
                currentLat = e.latlng.lat;
                currentLng = e.latlng.lng;

                document.getElementById('marker_type_select').value = marker.myMeta['marker-type'];
                document.getElementById('editMarker').style.display = 'block';
                document.getElementById('saveMarker').style.display = 'none';
                document.getElementById('removeMarker').style.display = 'block';
                document.querySelector('.pop-up-header').querySelector('p').textContent = 'Редагування мітки'


                openModal();
            }
        });
    }

// Один раз додаємо слухач при ініціалізації
    function initModalListeners() {
        document.getElementById('editMarker').addEventListener('click', function () {
            if (currentEditingMarker && currentLat !== null && currentLng !== null) {
                removeMarker(currentLat, currentLng, currentEditingMarker);
                const newMarker = addMarker(currentLat, currentLng);
                if (newMarker) {
                    currentEditingMarker = null;
                    closeModal();
                }
            }
        });

        document.querySelector('.modal-close').addEventListener('click', closeModal);
    }

    function removeMarker(lat, lng, marker) {
        const oldType = marker.myMeta['marker-type']; // ВАЖЛИВО!
        removeCoordinates([lat, lng], oldType);       // Видаляємо з правильного типу
        markersGroup.removeLayer(marker);
    }

    function addMarker(lat, lng, type = '') {
        let markerType
        if (type !== '') {
            markerType = type
        } else {
            const selectMarkerType = document.getElementById('marker_type_select')
            if (selectMarkerType.value === '') {
                return
            }
            markerType = selectMarkerType.value.trim()
        }
        const customIcon = L.divIcon({
            className: 'custom-svg-icon', // Можна для стилів
            html: markerTypes[markerType]['svg'],
            iconSize: [32, 32],
            iconAnchor: [16, 32], // Точка прикріплення
        });

        const marker = L.marker([lat, lng], {
            icon: customIcon
        }); // створення маркера

        // ВИПРАВЛЕННЯ: створюємо новий об'єкт для кожного маркера
        marker.myMeta = {
            'marker-type': markerType,
        };

        markers[markerType].push([lat, lng])
        markersGroup.addLayer(marker); // додавання в групу
        return marker
    }

    renderMapWithMarkers()
    allowEdit()
    listenMap()
    initModalListeners()
    initLoad()
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
        const previewDiv = mainDiv.querySelector('.container-hl-preview')
        data.forEach(elementBlock => {
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

            const btnRemove = createInputBtn("hl_btn_remove", 'Remove', actionBtnRemove)
            const btnEdit = createInputBtn("hl_btn_edit", 'Edit', actionEdit)

            divItemAction.appendChild(btnEdit)
            divItemAction.appendChild(btnRemove)

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

        createElement(mainDiv, dataArray, hlName)
        btnAdd.addEventListener('click', function () {
            let data = getHl(mainDiv, hlName)

            createElement(mainDiv, data, hlName)
        })
    })

}


document.addEventListener("DOMContentLoaded", function () {
    let tabs = ['main', 'gallery', 'map', 'contact']
    actionTab(tabs)
    let hlNames = ['contact','gallery']
    processHardLabel(hlNames)
    processMap()


})
