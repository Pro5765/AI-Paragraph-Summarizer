<?php

namespace TextSummarizer;

class TextAnalyzer {
    public function calculateImportance($sentence, $sentences) {
        $score = 0;
        $words = str_word_count(strtolower($sentence), 1);
        
        foreach ($sentences as $otherSentence) {
            if ($sentence !== $otherSentence) {
                $otherWords = str_word_count(strtolower($otherSentence), 1);
                $commonWords = array_intersect($words, $otherWords);
                $score += count($commonWords) / (log(count($words) + count($otherWords)));
            }
        }
        
        return $score;
    }
}