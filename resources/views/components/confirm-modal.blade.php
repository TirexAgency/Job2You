@props([
    'id',
    'title',
    'action',
    'method' => 'DELETE',
    'confirmLabel' => 'Confirmer',
    'confirmClass' => 'btn-danger',
    'message' => null,
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-5" id="{{ $id }}Label">{{ $title }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="POST" action="{{ $action }}">
                @csrf
                @method($method)
                <div class="modal-body text-secondary">
                    @if ($slot->isNotEmpty())
                        {{ $slot }}
                    @else
                        {{ $message }}
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn {{ $confirmClass }}">{{ $confirmLabel }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
