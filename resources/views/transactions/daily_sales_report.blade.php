@extends('layouts.master')
@section('content')

   @livewire('transactions.daily-sales-report')
@endsection

@section('script')
    <script>
        // function selectOrder(data, prices) {

        //     // Ensure ordered_items is defined and is an array
        //     if (!data.order.ordered_items || !Array.isArray(data.order.ordered_items)) {
        //         console.error('Invalid order details:', data.order.ordered_items);
        //         return;
        //     }
        //     //mag add ug value sa customer name
        //     document.getElementById('customer_name').value = data.customer_name || 'N/A';
        //     //mag add ug data sa table field
        //     document.getElementById('table_name').value = data.order.tables.table_name || 'N/A';
        //     //mag add ug data sa order number
        //     document.getElementById('order_number').value = data.order.order_number || 'N/A';

        //     // Access the table body
        //     const tableBody = document.getElementById('itemTableBody');
        //     tableBody.innerHTML = ''; // Clear existing rows

        //     let sumTotal = 0;

        //     // Populate the table with order items
        //     data.order.ordered_items.forEach(dtls => {
        //         const price = prices.shift();
        //         const subTotal = price * dtls.qty;
        //         sumTotal += subTotal;

        //         const newRow = document.createElement('tr');
        //         newRow.innerHTML = `
        //             <td style="font-size: smaller; ">${dtls.menu.menu_name}</td>
        //             <td style="font-size: smaller; ">${dtls.qty}</td>
        //             <td style="font-size: smaller; ">${price}</td>
        //             <td class="total-price" style="font-size: smaller; ">${subTotal.toFixed(2)}</td>
        //         `;
        //         tableBody.appendChild(newRow);
        //     });

        //     // Update total in the modal footer
        //     document.querySelector('.card-footer h6').textContent = `Total : ${sumTotal.toFixed(2)}`;
        // }
    </script>
@endsection
