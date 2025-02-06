@if($playerName === '')
    <div wire:key="game-session-{{ $session->id }}" class="flex justify-center items-center">
        <form wire:submit.prevent="createPlayer" class="space-y-4 flex flex-col items-center" style="padding-top: 10px;">
            <input
                type="text"
                wire:model.defer="playerName"
                placeholder="Your Name"
                class="w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                required
            >

            <div class="flex items-center space-x-2 w-full">
                <input
                    type="checkbox"
                    wire:model.defer="enableTimer"
                    id="enableTimer"
                    class="rounded border-gray-300 text-green-500 focus:ring-green-500"
                >
                <label for="enableTimer" class="text-sm font-medium text-gray-700">
                    Giới hạn thời gian
                </label>
            </div>

            <div class="w-full" x-show="$wire.enableTimer">
                <input
                    type="text"
                    wire:model.defer="customTimer"
                    placeholder="Thời gian (giây)"
                    class="w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                >
            </div>

            <button
                type="submit"
                class="btn-default btn--link bg-green-500 text-black py-2 rounded-lg hover:bg-green-600 focus:ring-2 focus:ring-green-500 focus:outline-none transition"
                style="width: 220px;"
            >
                Tạo phòng
            </button>
        </form>
    </div>
@else
    <div wire:key="game-session-{{ $session->id }}">
        <h2 class="text-xl font-bold mb-4">Game Code: {{ $session->code }}</h2>



        <h5 class="text-lg font-semibold mb-2">Players Joined ({{ count($players) }}):</h5>
        @foreach($players as $player)
                        <h6 class="font-medium mb-4">Player:  {{ $player['name'] ?? 'Unknown' }}</h6>

        @endforeach


        <!-- Nút Start Game -->
        @if(count($players) > 0)
            <button wire:click="startGame" class="btn-default btn--link  w-full bg-green-500 text-black py-2 rounded-lg hover:bg-green-600 focus:ring-2 focus:ring-green-500 focus:outline-none transition" style="
    width: 150px;
">
                Start Game
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
