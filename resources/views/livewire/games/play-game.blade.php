@if(!$sessionId)
    <form wire:submit.prevent="join">
        <input type="text" wire:model.defer="gameCode" placeholder="Enter Game Code">
        <input type="text" wire:model.defer="playerName" placeholder="Your Name">
        <button type="submit">Join Game</button>
    </form>
@elseif($sessionStatus === 'waiting')
    <div>
        Waiting for host to start the game...
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Kiểm tra xem session có tồn tại không và sử dụng code của session
        if('{{ $session->code }}') {
            window.Echo.channel('game.{{ $session->code }}')
                .listen('GameStarted', (event) => {
                    console.log('Game started:', event);
                    // Gọi phương thức handleGameStart từ Livewire để điều hướng
                @this.call('handleGameStart', event);
                });
        }
    });
</script>
