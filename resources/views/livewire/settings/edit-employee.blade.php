<div>
    <!-- Edit Employee Modal -->
    <div class="modal fade" id="edit-employee-modal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Edit Employee</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="update">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Corporate ID</label>
                                <div class="input-group">
                                    <input type="text" wire:model="corporate_id" class="form-control" placeholder="Enter Corporate ID">
                                    <button type="button" wire:click="fetchFromHris" class="btn btn-outline-primary">Fetch from HRIS</button>
                                </div>
                                @error('corporate_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control" required placeholder="Enter First Name">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" wire:model="middle_name" class="form-control" placeholder="Enter Middle Name">
                                @error('middle_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" wire:model="last_name" class="form-control" required placeholder="Enter Last Name">
                                @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" wire:model="contact_number" class="form-control" placeholder="Enter Contact Number">
                                @error('contact_number') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Position</label>
                                <select wire:model="position_id" class="form-select">
                                    <option value="">Select Position</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                                    @endforeach
                                </select>
                                @error('position_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Religion</label>
                                <input type="text" wire:model="religion" class="form-control" placeholder="Enter Religion">
                                @error('religion') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Birth Date</label>
                                <input type="date" wire:model="birth_date" class="form-control">
                                @error('birth_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Branch <span class="text-danger">*</span></label>
                                <select wire:model="branch_id" class="form-select" required>
                                    <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Department</label>
                                <select wire:model="department_id" class="form-select">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select wire:model="status" class="form-select" required>
                                    <option value="ACTIVE">Active</option>
                                    <option value="INACTIVE">Inactive</option>
                                </select>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" wire:click="closeModal" data-bs-dismiss="modal" class="btn btn-secondary me-2">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>