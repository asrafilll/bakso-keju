<x-app>
    @if (Session::has('success'))
        <script>
            toastr.success('{{ Session::get('success') }}');
        </script>
    @endif

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row justify-content-between mb-2">
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Inventories') }}</h1>
                </div><!-- /.col -->
                @can(\App\Enums\PermissionEnum::create_inventory()->value)
                    <div class="col-auto">
                        <a href="{{ url('/component-inventories/create') }}"
                            class="btn btn-primary">{{ __('Create inventory') }}</a>
                    </div><!-- /.col -->
                @endcan
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <form action="" method="GET">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                            <input type="search" name="term" class="form-control"
                                                value="{{ Request::get('term') }}"
                                                placeholder="{{ __('Filter item inventories') }}" />
                                        </div>
                                    </form>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-default" data-toggle="modal"
                                        data-target="#filterModal">
                                        <i class="fas fa-filter"></i>
                                        <span>{{ __('Filter') }}</span>
                                    </button>
                                    <div class="modal fade" id="filterModal" tabindex="-1"
                                        aria-labelledby="filterModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="" method="GET">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="filterModalLabel">
                                                            {{ __('Filter') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="term">
                                                                <span>{{ __('Filter item inventories') }}</span>
                                                            </label>
                                                            <input type="search" name="term" class="form-control"
                                                                value="{{ Request::get('term') }}" />
                                                        </div>
                                                        <div class="form-group" id="branch-module">
                                                            <label for="branch_id">
                                                                <span>{{ __('Branch') }}</span>
                                                            </label>
                                                            <select id="branch_id" name="branch_id"
                                                                class="form-control @error('branch_id') is-invalid @enderror"
                                                                style="width: 100%;">
                                                                <option></option>
                                                                @if (Request::get('branch_id') && Request::get('branch_name'))
                                                                    <option value="{{ Request::get('branch_id') }}"
                                                                        selected>{{ Request::get('branch_name') }}
                                                                    </option>
                                                                @endif
                                                            </select>
                                                            <input type="hidden" id="branch_name" name="branch_name"
                                                                value="{{ Request::get('branch_name') }}" />
                                                            @error('branch_id')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <script>
                                                            var BranchModule = (function() {
                                                                var $el = $('#branch-module');
                                                                var $branchId = $el.find('#branch_id');
                                                                var $branchName = $el.find('#branch_name');

                                                                $branchId.on('select2:select', function(e) {
                                                                    $branchName.val(e.params.data.text);
                                                                });

                                                                $branchId.on('select2:unselect', function() {
                                                                    $branchName.val(null);
                                                                });

                                                                $branchId.on('select2:clear', function() {
                                                                    setTimeout(function() {
                                                                        $branchId.select2('close');
                                                                    }, 0);
                                                                });

                                                                function init() {
                                                                    $branchId.select2({
                                                                        theme: 'bootstrap4',
                                                                        placeholder: '',
                                                                        allowClear: true,
                                                                        ajax: {
                                                                            url: '/item-inventories?action=fetch-branches',
                                                                            dataType: 'json',
                                                                            delay: 250,
                                                                            processResults: function(branches) {
                                                                                return {
                                                                                    results: branches.map(function(branch) {
                                                                                        return {
                                                                                            id: branch.id,
                                                                                            text: branch.name,
                                                                                            branch: branch,
                                                                                        };
                                                                                    }),
                                                                                };
                                                                            },
                                                                        },
                                                                    });
                                                                }

                                                                init();
                                                            })()
                                                        </script>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">{{ __('Close') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ __('Save') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ Request::fullUrlWithQuery([
                                        'action' => 'export',
                                    ]) }}"
                                        class="btn btn-default">
                                        <i class="fas fa-download"></i>
                                        <span>{{ __('Export') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Branch') }}</th>
                                        <th>{{ __('Component') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Date updated') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($componentInventories as $componentInventory)
                                        <tr>
                                            <td class="align-middle">{{ $componentInventory->branch_name }}</td>
                                            <td class="align-middle">{{ $componentInventory->product_component_name }}
                                            </td>
                                            <td class="align-middle">{{ $componentInventory->quantity }}</td>
                                            <td class="align-middle">{{ $componentInventory->updated_at }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">{{ __('Data not found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex justify-content-center">
                            {!! $componentInventories->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
