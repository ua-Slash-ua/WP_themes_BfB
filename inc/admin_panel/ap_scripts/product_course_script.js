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
            item.addEventListener('dragstart', function(e) {
                draggedItem = item;
                setTimeout(() => {
                    item.classList.add('dragging');
                }, 0);
            });

            // When drag ends
            item.addEventListener('dragend', function() {
                item.classList.remove('dragging');
                draggedItem = null;
                // Update data after dragging is complete
                pointUpdateForRestApi(pointName);
            });

            // Prevent default behaviors for some events
            item.addEventListener('dragover', function(e) {
                e.preventDefault();
            });

            item.addEventListener('dragenter', function(e) {
                e.preventDefault();
                if (this !== draggedItem) {
                    this.classList.add('drag-over');
                }
            });

            item.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            // Handle dropping
            item.addEventListener('drop', function(e) {
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
        container.addEventListener('dragover', function(e) {
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

        container.addEventListener('drop', function(e) {
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
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }
    }
    function pointAdd(pointName, pointValue){

        const pointItemMain = document.getElementById(`point_container_${pointName}`)

        const pointItemDiv = document.createElement('div')
        pointItemDiv.classList.add('point_item')


        const pointItemSpan = document.createElement('span')
        pointItemSpan.textContent = pointValue

        const pointItemBtn = document.createElement('input')
        pointItemBtn.type='button'
        pointItemBtn.value='x'
        pointItemBtn.classList.add('point_del')
        pointItemBtn.addEventListener('click', function (){
            pointItemMain.removeChild(pointItemDiv)
            pointUpdateForRestApi(pointName)
        })

        pointItemDiv.appendChild(pointItemSpan)
        pointItemDiv.appendChild(pointItemBtn)
        pointItemMain.appendChild(pointItemDiv)
        pointUpdateForRestApi(pointName)
        enableDragAndDrop(pointItemMain, pointName)
    }

    function pointUpdateForRestApi(pointName){
        const pointItemMain = document.getElementById(`point_container_${pointName}`)
        const pointData = document.getElementById(`point_data_${pointName}`)
        let pointDataArray = []

        pointItemMain.querySelectorAll('.point_item').forEach( item =>{
            const pointValue = item.querySelector('span').textContent.trim()
            pointDataArray.push(pointValue)
        })

        pointData.setAttribute('value',JSON.stringify(pointDataArray))

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

    pointNames.forEach(pointName =>{
        pointLoad(pointName)
        document.getElementById(`point_add_${pointName}`).addEventListener('click', function (){
            const pointValue = document.getElementById(`point_input_${pointName}`)

            if (!pointValue.value.trim()) {
                // Додаємо клас одразу
                pointValue.classList.add('error');

                // А потім через деякий час — прибираємо, щоб ефект був тимчасовий
                setTimeout(function () {
                    pointValue.classList.remove('error');
                }, 1500); // наприклад, через 0.8 сек
            }else {
                pointAdd(pointName, pointValue.value.trim())
                pointValue.value = ''

            }

        })
    })


}

document.addEventListener('DOMContentLoaded',function (){

    let points = ['course_themes']
    processPoint(points)
    let tabs = ['main', 'program']
    actionTab(tabs)
})