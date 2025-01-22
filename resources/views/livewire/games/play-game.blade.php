<div>
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

    @if($session)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const gameCode = '{{ $session->code }}';
                console.log('Script loaded and listening for events on channel:', gameCode);

                if (gameCode) {
                    window.Echo.channel(`game.${gameCode}`)
                        .listen('GameStarted', (event) => {
                            console.log('GameStarted event received:', event);
                        @this.call('handleGameStart', event);
                        })
                        .error((error) => {
                            console.error('Error listening to channel:', error);
                        });
                }
            });
        </script>
    @endif
</div>
