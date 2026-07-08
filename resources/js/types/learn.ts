export type WordPart = {
    text: string;
    translation?: string;
};

export type WordToken = {
    text: string;
    translation?: string;
    parts?: WordPart[];
};

export type ExerciseType =
    | 'complete_sentence_input'
    | 'complete_sentence_mcq'
    | 'reorder_translation'
    | 'concept_text';

export type Exercise = {
    id: number;
    course_id: number;
    lesson_id: number | null;
    type: ExerciseType;
    prompt: string;
    answer: string | string[];
    options: string[] | null;
    explanation: string | null;
    order: number;
    metadata: {
        word_tokens?: WordToken[];
        [key: string]: unknown;
    } | null;
    created_at: string;
    updated_at: string;
};

export type Lesson = {
    id: number;
    course_id: number;
    title: string;
    description: string | null;
    order: number;
    exercises?: Exercise[];
    created_at: string;
    updated_at: string;
};

export type Course = {
    id: number;
    title: string;
    description: string | null;
    language_from: string;
    language_to: string;
    is_published: boolean;
    exercises_count?: number;
    lessons_count?: number;
    lessons?: Lesson[];
    created_at: string;
    updated_at: string;
};

export type UserCourseProgress = {
    id: number;
    user_id: number;
    course_id: number;
    current_exercise_id: number | null;
    completed_exercise_ids: number[];
    score: number;
    created_at: string;
    updated_at: string;
};

export type AnswerResult = {
    correct: boolean;
    explanation: string | null;
    correct_answer: string | string[];
};
