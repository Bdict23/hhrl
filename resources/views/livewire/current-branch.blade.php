<div>
   <button type="button" class="btn btn-light" 
      data-bs-target="#switchBranchModal" data-bs-toggle="modal">
        {{ Auth::user()->branch()->first()->branch_name ?? ''}}
        @if (Auth::user()->branch()->first()->branch_name !=null)
            @if (Auth::user()->branch()->first()->branch_status == 'INACTIVE')
              <span data-bs-toggle="tooltip" data-bs-placement="top" title="Switch branch" style="color: rgb(219, 69, 69);">(INACTIVE)</span>
            @endif
        @endif
      </button>

    <!-- Modal -->
    <div class="modal fade" id="switchBranchModal" tabindex="-1" aria-labelledby="switchBranchModalLabel" aria-hidden="true" wire:ignore.self>
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="switchBranchModalLabel">Switch Branch</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
         
            @csrf
            <div class="modal-body">
              <div class="mb-3">
                <label for="branchSelect" class="form-label">Select Branch</label>
                <select class="form-select" id="branchSelect" name="branch_id" wire:model.live="currentSwitch">
                  <option value="" disabled>Select Branch</option>
                    @forelse($branches as $branch)
                        <option value="{{ $branch->branch_id }}" {{ auth()->user()->branch_id == $branch->branch_id ? 'selected' : '' }}>
                        {{ $branch->branch->branch_name }}{{ $branch->branch->branch_status == 'INACTIVE' ? ' (INACTIVE)' : '' }}
                        </option>
                    @empty
                        <option value=""> {{ auth()->user()->branch->branch_name ?? '' }}</option>
                    @endforelse
                  {{-- @foreach(Auth::user()->branches as $branch)
                    <option value="{{ $branch->id }}" {{ Auth::user()->branch()->first()->id == $branch->id ? 'selected' : '' }}>
                      {{ $branch->branch_name }}{{ $branch->branch_status == 'INACTIVE' ? ' (INACTIVE)' : '' }}
                    </option>
                  @endforeach --}}
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button wire:click="switchBranch()" type="button" class="btn btn-primary" data-bs-dismiss="modal">Switch</button>
            </div>
          
        </div>
      </div>
    </div>



</div>
