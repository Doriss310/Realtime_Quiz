public function submit()
{
$result = 0;
$test = Test::create([
'user_id' => auth()->id(),
'quiz_id' => $this->quiz->id,
'result' => 0,
'ip_address' => request()->ip(),
'time_spent' => now()->timestamp - $this->startTimeInSeconds
]);

foreach ($this->answersOfQuestions as $key => $answer) {
$question = $this->questions[$key];

if ($question->question_type === 'code_snippet') {
$isCorrect = trim($answer) === trim($question->code_snippet);
if ($isCorrect) $result++;

Answer::create([
'user_id' => auth()->id(),
'test_id' => $test->id,
'question_id' => $question->id,
'code_answer' => $answer,
'correct' => $isCorrect ? 1 : 0
]);
} else {
// Handle both single and multiple choice
$optionIds = is_array($answer) ? $answer : [$answer];
$correctOptions = $question->options()->where('correct', true)->pluck('id')->toArray();

sort($optionIds);
sort($correctOptions);

$isCorrect = $optionIds === $correctOptions;
if ($isCorrect) $result++;

foreach ($optionIds as $optionId) {
Answer::create([
'user_id' => auth()->id(),
'test_id' => $test->id,
'question_id' => $question->id,
'option_id' => $optionId,
'correct' => $isCorrect ? 1 : 0
]);
}
}
}

$test->update(['result' => $result]);
return to_route('results.show', ['test' => $test]);
}
