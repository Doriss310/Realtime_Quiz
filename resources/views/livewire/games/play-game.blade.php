<div class="flex justify-center items-center">
    @if(!$session)
        <form wire:submit.prevent="join" style="
    padding-top: 10px;
    display: flex;
    flex-direction: column;
    align-content: center;
    align-items: center;
">
            <input type="text" wire:model.defer="gameCode" placeholder="Enter Game Code" class="w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" required>
            <input type="text" wire:model.defer="playerName" placeholder="Your Name" class=" w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" required>
            <button type="submit" class="btn-default btn--link  w-full bg-green-500 text-black py-2 rounded-lg hover:bg-green-600 focus:ring-2 focus:ring-green-500 focus:outline-none transition" style="
             width: 220px;">Join Game</button>
        </form>
    @elseif($sessionStatus === 'waiting')
        <div class="text-center">
            <h3>Waiting for host to start the game...</h3>
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
