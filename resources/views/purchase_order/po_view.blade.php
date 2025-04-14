@extends('layouts.master')
@section('content')

    <div>

        @livewire('purchase-order-show', ['id' => $requestInfo->id])

    </div>
@endsection

@section('script')
    <script>
        document.querySelectorAll('.editable').forEach(cell => {
            cell.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent newline
                    this.blur(); // Trigger the blur event
                }
            });

            cell.addEventListener('blur', function() {
                const row = this.closest('tr');
                const id = row.getAttribute('data-id');
                const column = this.getAttribute('data-column');
                const value = this.innerText;

                fetch('/update-receive-qty', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id,
                            column,
                            value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Updated successfully!');
                        } else {
                            alert('Failed to update.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });



        function updateStatus(currentStep) {
            const steps = document.querySelectorAll('.status-flow .step');
            steps.forEach((step, index) => {
                if (index < currentStep) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (index === currentStep) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('completed', 'active');
                }
            });
        }
    </script>
@endsection
