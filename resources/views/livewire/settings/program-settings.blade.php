<div >
    <div id="program-settings" class="tab-content card" style="display: none;" wire:ignore.self>

        <div class="card-header ">
          <div class="d-flex justify-content-between">
            <h5>Program Settings</h5>
            <span wire:loading>
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              Applying...
              </span>  
            </div>
        </div>
        {{-- <div class="card-body">
               <div class="p-1 mt-3 mb-3 justify-content-center ">
                 <div class="table-responsive mt-3 mb-3 d-flex justify-content-between ">
                     <label class="form-check-label text-end" for="allow-purchase-order-reviewer">Allow Reviewer on Purchase Order</label>
                     <label class="toggle-switch">
                         <input type="checkbox" checked>
                         <span class="slider"></span>
                     </label>                      
                 </div>
                 <div class="table-responsive mt-3 mb-3 d-flex justify-content-between ">
                     <label class="form-check-label text-end" for="allow-purchase-order-reviewer">Allow Reviewer on Withdrawal</label>
                     <label class="toggle-switch">
                         <input type="checkbox" checked>
                         <span class="slider"></span>
                     </label>
                 </div>
               </div>
        </div> --}}

        <div class="card">
            
            <div class="card-body table-responsive-sm" style="height: 300px; overflow-y: auto;">  
                          
                 <table class="table table-striped table-sm">
                    <thead class="table-dark sticky-top">
                        <tr class="text-smaller">
                            <th>Setting Name</th>
                            <th >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branches as $branch)
                            <tr> 
                                <td colspan="4" class="text-center" style="background-color: #c9f5df;"><strong>{{ $branch->branch_name }}</strong></td>
                            </tr>
                        
                            @foreach ($programSettings as $setting)
                            <tr>
                                <td class="text-smaller">{{ $setting->setting_name }}</td>
                                    <td >
                                        <select wire:change="setBranchConfiguration({{ $branch->id }}, {{ $setting->id}} , $event.target.value )"  class="form-select" aria-label="Default select example">
                                            <option value="" disabled>Select Access</option>
                                            <option value = "0" {{ $branchConfiguration[$branch->id][$setting->id]==0 ? 'selected': ''}} >Not Allow</option>
                                            <option value = "1" {{ $branchConfiguration[$branch->id][$setting->id]==1 ? 'selected': ''}} >Allow</option>
                                        </select>
                                    </td>   
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <style>
          
    .settings-container {
      max-width: 400px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .setting-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 0;
      border-bottom: 1px solid #ddd;
    }
    .setting-item:last-child {
      border-bottom: none;
    }
    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 26px;
    }
    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0; left: 0;
      right: 0; bottom: 0;
      background-color: #ccc;
      transition: 0.4s;
      border-radius: 26px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 22px;
      width: 22px;
      left: 2px;
      bottom: 2px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
    }
    input:checked + .slider {
      background-color: #4caf50;
    }
    input:checked + .slider:before {
      transform: translateX(24px);
    }
    </style>

</div>
