<div>
    <div id="classification-table" class="tab-content card" style="display: none;">
        <div class="card-header">
            <h5>Classification</h5>
        </div>
        <div class="card-body">
            <x-primary-button type="button" class="mb-3"
                onclick="showTab('classification-form', document.querySelector('.nav-link.active'))">+ Add
                Classification</x-primary-button>
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">STATUS</th>
                            <th class="text-end">Sub Classes</th>
                            <th class="text-end">REG. COMPANY</th>
                            <th class="text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classifications as $classification)
                            <tr>
                                <td>{{ $classification->classification_name }}</td>
                                <td>{{ $classification->classification_description }}</td>
                                <td class="text-end">{{ $classification->status }}</td>
                                <td class="text-end">
                                    {{ optional($classification->sub_classifications)->count() ?? 0 }}
                                </td>
                                <td class="text-end">
                                    {{ $classification->company->company_name ?? 'Not Registered' }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No classification found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Classification Form --}}
    <div id="classification-form" class="tab-content card" style="display: none;">
        <div class="card-header">
            <h5>Add Classification</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3"
                onclick="showTab('classification-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
            <form action="{{ route('settings.classification.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Classification Name <span
                            style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="reg_company" class="form-label">Belongs to company <span
                            style="color: red;">*</span></label>
                    <select class="form-control" id="reg_company" name="reg_company" required>
                        @forelse ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @empty
                            <option value="no_company">No Company Available</option>
                        @endforelse
                    </select>
                </div>
                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>
</div>
