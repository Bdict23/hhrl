@extends('layouts.master')
@section('content')
   


    <div>
       @livewire('purchase-order-review-show', ['id' => $requestInfo->id])
    </div>
@endsection

@section('script')
    <script>
        function updateStatus2() {
            fetch('{{ url('/update-requisition-status/' . $requestInfo->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: 'FOR APPROVAL'
                    })
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/review_request_list';
                    } else {
                        alert('Failed to update requisition status.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
        }
    </script>
@endsection
