@extends('layouts.master')

@section('content')

    <div class="container mt-5">
        <ul class="nav nav-tabs" id="jobOrderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice" type="button"
                    role="tab" aria-controls="invoice" aria-selected="true">FLOOR</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="job-order-tab" data-bs-toggle="tab" data-bs-target="#job-order" type="button"
                    role="tab" aria-controls="job-order" aria-selected="false">
                    KITCHEN</button>
            </li>
        </ul>
        
            @livewire('orders')
                    
    </div>

      <script>
        function updateLapsedTime() {
            const lapsedTimeElements = document.querySelectorAll('.lapsed-time');
            lapsedTimeElements.forEach(element => {
                const createdAt = new Date(element.getAttribute('data-time'));
                const now = new Date();
                const diff = Math.floor((now - createdAt) / 1000); // Difference in seconds

                const hours = Math.floor(diff / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                const seconds = diff % 60;

                element.textContent = `${hours}h ${minutes}m ${seconds}s`;
            });
        }

        setInterval(updateLapsedTime, 1000);
        // Drag and Drop functionality
        const draggables = document.querySelectorAll('.draggable');
        const container = document.getElementById('orderContainer');

        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', () => {
                draggable.classList.add('dragging');
            });

            draggable.addEventListener('dragend', () => {
                draggable.classList.remove('dragging');
            });
        });

        container.addEventListener('dragover', e => {
            e.preventDefault();
            const afterElement = getDragAfterElement(container, e.clientY);
            const draggable = document.querySelector('.dragging');
            if (afterElement == null) {
                container.appendChild(draggable);
            } else {
                container.insertBefore(draggable, afterElement);
            }
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return {
                        offset: offset,
                        element: child
                    };
                } else {
                    return closest;
                }
            }, {
                offset: Number.NEGATIVE_INFINITY
            }).element;
        }
    </script>
@endsection

  
