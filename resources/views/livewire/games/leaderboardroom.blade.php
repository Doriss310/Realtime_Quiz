@vite('resources/css/app.css')
@include('layouts.navigation')
<x-app-layout>
<div>
    <h2 class="font-semibold text-xl text-white leading-tight">
        Leaderboard - Session {{ $session->code }}
    </h2>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="table mt-4 w-full table-view">
                        <thead>
                        <tr>
                            <th class="bg-gray-50 px-6 py-3 text-left w-9">#</th>
                            <th class="bg-gray-50 px-6 py-3 text-left w-1/2">Tên người chơi</th>
                            <th class="bg-gray-50 px-6 py-3 text-left">Điểm số</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($players as $player)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $player->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $player->score }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm text-gray-900 text-center">
                                    Không có người chơi.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
