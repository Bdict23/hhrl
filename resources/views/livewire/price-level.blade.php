<div id="price-levels-tables" class="tab-content" style="display: none;">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="price-levels-tab" data-bs-toggle="tab" href="#price-levels-table" role="tab"
                aria-controls="price-levels-table" aria-selected="true">Price Levels</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="price-levels-form-tab" data-bs-toggle="tab" href="#price-levels-form" role="tab"
                aria-controls="price-levels-form" aria-selected="false">Price Level Form</a>
        </li>
    </ul>
    <div id="myTabContent">
        <div id="price-levels-table" class="tab-pane fade show card active" role="tabpanel"
            aria-labelledby="price-levels-tab">
            <x-primary-button type="button" class="mb-3 btn-sm">+ ADD PRICE LEVEL</x-primary-button>
            <table class="table table-striped table-sm small">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>DESCRIPTION</th>
                        <th class="text-end">STATUS</th>
                        <th class="text-end">REG. COMPANY</th>
                        <th class="text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center">No price level found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
