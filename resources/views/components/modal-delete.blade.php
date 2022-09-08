<div
    class="modal fade"
    id="modal-delete"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Delete') }}</h4>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you sure want to delete?') }}</p>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                    >{{ __('No') }}</button>
                    <button
                        type="submit"
                        class="btn btn-danger"
                    >{{ __('Yes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#modal-delete').on('show.bs.modal', function(event) {
        const $btn = $(event.relatedTarget);
        const action = $btn.data('action');
        $(this)
            .find('form')
            .attr('action', action);
    });
</script>
