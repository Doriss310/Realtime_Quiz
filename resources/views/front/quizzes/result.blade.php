<x-app-layout>
    <div class="py-12">
        @vite('resources/css/app.css')
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-[#0a0a23] shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <h6 class="text-xl font-bold">Kết quả kiểm tra</h6>

                    <table class="mt-4 table w-full table-view">
                        <tbody class="bg-white">
                        @if (auth()->user()?->is_admin)
                            <tr class="w-28">
                                <th
                                    class="border border-solid bg-gray-100 px-6 py-3 text-left text-sm font-semibold uppercase text-slate-600">
                                    Người dùng</th>
                                <td class="border border-solid px-6 py-3">{{ $test->user->name ?? '' }}
                                    ({{ $test->user->email ?? '' }})</td>
                            </tr>
                        @endif
                        <tr class="w-28">
                            <th
                                class="border border-solid bg-gray-100 px-6 py-3 text-left text-sm font-semibold uppercase text-slate-600">
                                Thời gian</th>
                            <td class="border border-solid px-6 py-3">
                                {{ $test->created_at->format('D m/Y, h:m A') ?? '' }}</td>
                        </tr>
                        <tr class="w-28">
                            <th
                                class="border border-solid bg-gray-100 px-6 py-3 text-left text-sm font-semibold uppercase text-slate-600">
                                Kết quả</th>
                            <td class="border border-solid px-6 py-3">
                                {{ $test->result }} / {{ $questions_count }}
                                @if ($test->time_spent)
                                    (Thời gian: {{ sprintf('%.2f', $test->time_spent / 60) }}
                                    Phút)
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <br>
        @isset($leaderboard)
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-12">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h6 class="text-xl font-bold">Bảng xếp hạng</h6>

                        <table class="table mt-4 w-full table-view">
                            <thead>
                            <th class="text-left">Hạng</th>
                            <th class="text-left">Tên người dùng</th>
                            <th class="text-left">Kết quả</th>
                            </thead>
                            <tbody class="bg-white">
                            @foreach ($leaderboard as $test)
                                <tr @class([
                                        'bg-gray-100' => auth()->user()->name == $test->user->name,
                                    ])>
                                    <td class="w-9">{{ $loop->iteration }}</td>
                                    <td class="w-1/2">{{ $test->user->name }}</td>
                                    <td>{{ $test->result }} / {{ $questions_count }} (Thời gian:
                                        {{ sprintf('%.2f', $test->time_spent / 60) }} Phút)
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endisset
        <br>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @php
                        $groupedResults = $results->groupBy('question_id');
                    @endphp

                    @foreach ($groupedResults as $questionId => $questionResults)
                        @php
                            $question = $questionResults->first()->question;
//                            dd($questionResults->first());
                        @endphp
                        {{--  dd($questionResults) --}}
{{--                          dd($questionResults->first())--}}
                        <table class="table table-view w-full my-4 bg-white">
                            <tbody class="bg-white">
                            <tr class="bg-gray-100">
                                <td class="w-1/2">Câu #{{ $loop->iteration }}</td>
                                <td>{!! nl2br($question->text) !!}</td>
                            </tr>
                            <tr>
                                <td>Các lựa chọn</td>
                                <td>
                                    @foreach ($question->options as $option)
                                        @if($questionResults->first()->code_answer !== null)
                                        <li @class([
                            'underline' => $questionResults->contains('option_id', $option->id),
                            'font-bold' => $option->correct == 1,
                        ])>
                                            {{ $option->text }} </li>
                                            @else
                                            <li @class([
                            'underline' => $questionResults->contains('option_id', $option->id),
                            'font-bold' => $option->correct == 1,
                        ])>
                                                {{ $option->text }}
                                            @if ($option->correct == 1)
                                                <span class="italic">(đáp án đúng)</span>
                                            @endif
                                            @php
                                                $userSelected = $questionResults->pluck('option_id')->contains($option->id);
                                            @endphp
                                            @if ($userSelected)
                                                <span class="italic">(bạn đã chọn)</span>
                                            @endif
                                        </li>
                                        @endif
                                    @endforeach
                                    @if($questionResults->first()->code_answer !== null)
                                        <p style="font-size: 1rem;font-weight: bold;">Đáp án code của bạn: {{$questionResults->first()->code_answer}}</p>
                                            <p style="font-size: 1rem;font-weight: bold;">Đáp án code đúng: {{$question->code_snippet}}</p>
                                        @endif
                                </td>
                            </tr>
                            @if ($question->answer_explanation || $question->more_info_link)
                                <tr>
                                    <td>Giải thích đáp án</td>
                                    <td>
                                        {{ $question->answer_explanation }}
                                    </td>
                                </tr>
                                @if ($question->more_info_link)
                                    <tr>
                                        <td>
                                            Xem thêm:
                                        </td>
                                        <td>
                                            <div class="mt-4">
                                                <a href="{{ $question->more_info_link }}"
                                                   class="hover:underline" target="_blank">
                                                    {{ $question->more_info_link }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                            </tbody>
                        </table>

                        @if (!$loop->last)
                            <hr class="my-4 md:min-w-full">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
