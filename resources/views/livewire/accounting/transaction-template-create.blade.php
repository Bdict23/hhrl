<div>
   
    <div class="d-flex justify-content-between">
        <x-secondary-button>Summary</x-secondary-button>
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-2">Accounting Setup: Master Data & Templates</h2>
    </div>

    <div class="row container mt-3">
        <div class="col-md-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <strong>Particulars</strong>
                        <x-primary-button>+ Add</x-primary-button>
                    </div>
                </div>
                <div class="card mt-2">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Office Supplies Purchase</td>
                                <td>Office Supplies Expense</td>
                                <td>Cash</td>
                                <td>Template for recording office supplies purchases</td>
                                <td>
                                    <x-secondary-button>Edit</x-secondary-button>
                                    <x-danger-button>Delete</x-danger-button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card row">
                <div class="input-group mt-2 mb-2">
                    <label for="" class="input-group-text">Account Type</label>
                    <input type="text" class="form-control" placeholder="Select account type ->" aria-label="Account Type" disabled>
                    <button class="btn btn-info"><i class="bi bi-bookmark-fill"></i></button>
                </div>
                <div class="input-group mb-2">
                    <label for="" class="input-group-text">Account Title</label>
                    <input type="text" class="form-control" placeholder="Select account title ->" aria-label="Account Title" disabled>
                    <button class="btn btn-info"><i class="bi bi-bookmark-fill"></i></button>
                </div>
                    <div>
                        <label for="" class="form-label">Description</label>
                        <textarea name="" id="" cols="30" rows="5" class="form-control" placeholder="Enter description here..."></textarea>
                    </div>
            </div>
        </div>

    </div>
   
</div>