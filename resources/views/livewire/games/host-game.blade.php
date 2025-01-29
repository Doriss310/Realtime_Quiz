@if($playerName === '')
    <div wire:key="game-session-{{ $session->id }}">
    <form wire:submit.prevent="createPlayer">
        <input type="text" wire:model.defer="playerName" placeholder="Your Name">
        <button type="submit">Create</button>
    </form>
    </div>
@else
    <div wire:key="game-session-{{ $session->id }}">
            <h2 class="text-xl font-bold">Game Code: {{ $session->code }}</h2>



            <h3 class="text-lg font-semibold mb-2">Players Joined ({{ count($players) }}):</h3>
                @foreach($players as $player)
            <div>
                <div class="p-3 rounded">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Player: {{ $player['name'] ?? 'Unknown' }}</span>
                        </div>
                    </div>
            </div>
                @endforeach


        <!-- Nút Start Game -->
        @if(count($players) > 0)
            <button
                wire:click="startGame"
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
            >
                Start Game
            </button>
            <button
                wire:click="nextQuestion"
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
            >
                Next Question
            </button>
        @else
            <div class="text-gray-500">
                Waiting for players to join...
            </div>
        @endif
    </div>

@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.Echo.channel('game.{{ $session->code }}')
            .listen('PlayerJoined', (event) => {
                console.log('Player Joined:', event);
                // Bạn có thể thêm logic để cập nhật danh sách người chơi
            @this.call('handlePlayerJoined', event);
            })
            .listen('AnswerSubmitted', (event) => {
                console.log('Answer Submit:', event);

            @this.call('handleAnswer', event);
            })
            .listen('GameStarted', (event) => {
                console.log('Game Start:', event);
            @this.call('handleGameStarted', event);
            });
    });
</script>
