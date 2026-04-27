  <div>
      <div>
          <h4 class="text-end">Transaction Templates - Summary <i class="bi bi-file-text"></i></h4>
      </div>
      <div class="mt-3 mb-3 card">
          <div class="card-body ">
              <div style="height: 500px; overflow-x: auto; display: block;">
                  <table class="table table-striped table-hover table-sm ">
                      <thead class="z-0 table-dark sticky-top">
                          <tr>
                              <th>Status</th>
                              <th>Business Unit</th>
                              <th>Type</th>
                              <th>Description</th>
                              <th>Created By</th>
                              <th>Created Date</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody>
                          @forelse ($templates ?? [] as $template)
                              <tr>
                                  <td><span
                                          @if ($template->is_active == 1) class= "badge bg-success" @else class= "badge bg-danger" @endif>{{ $template->is_active == 1 ? 'ACTIVE' : 'INACTIVE' }}</span>
                                  </td>
                                  <td>{{ $template->company->company_name }}</td>
                                  <td>{{ $template->type->type_name }}</td>
                                  <td>{{ $template->description }}</td>
                                  <td>{{ $template->createdBy->name }} {{ $template->createdBy->last_name }}</td>
                                  <td>{{ $template->created_at->format('M. d, Y') }}</td>
                                  <td>
                                      <a
                                          href="{{ route('template.approval.view', ['template' => $template->id, 'action' => 'approval']) }}">
                                          <x-primary-button> View </x-primary-button>
                                      </a>
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="10" class="text-center">No Data found</td>
                              </tr>
                          @endforelse

                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>
