<?php

/**
 * Produces a CSV out of users answers to a given WP-Pro-Quiz
 *
 * @link       http://www.battarra.it
 * @since      1.0.0
 *
 * @package    Wp_Pro_Quiz_Export
 * @subpackage Wp_Pro_Quiz_Export/admin/partial
 */
if (isset($_POST['checksum']) && wp_verify_nonce($_POST['checksum'], 'csv-export')) {
    global $wpdb;

    $quizID = $_POST[$this->plugin_name . '-quiz'];

    // useful URLs to point to for further information about entities retrieved by the query (quiz, question, wp user, ...)
    $arURLs = [
        'quiz_edit' => admin_url('admin.php?page=wpProQuiz&action=addEdit&quizId=%d'),
        'question_edit' => admin_url('admin.php?page=wpProQuiz&module=question&action=addEdit&quiz_id=%d&questionId=%d'),
        'wp_user_edit' => admin_url('user-edit.php?user_id=%d')
    ];

    // columns of the CSV
    $arExportFields = [
        'quiz_id',
        'quiz_name',
        'quiz_url',
        'question_id',
        'question_title',
        'question_url',
        'user_id',
        'user_wp_id',
        'user_wp_url',
        'user_quiz_completed_at',
        'answer_correct',
        'answer_incorrect',
        'answer_solved',
        'answer_points',
        'answer_time',
        'answers'
    ];
    $arExportFields = array_fill_keys($arExportFields, null);

    // quiz properties
    $quizSQL = 'SELECT * FROM cibq_wp_pro_quiz_master WHERE id=%d;';
    $arQuiz = $wpdb->get_results(sprintf($quizSQL, $quizID), ARRAY_A);
    if (!empty($arQuiz)) {
        $arQuiz = array_shift($arQuiz);

        $arExportFields['quiz_id'] = $arQuiz['id'];
        $arExportFields['quiz_name'] = $arQuiz['name'];
        $arExportFields['quiz_url'] = sprintf($arURLs['quiz_edit'], $arQuiz['id']);
    }

    // quiz questions
    $questionsSQL = 'SELECT * FROM cibq_wp_pro_quiz_question WHERE quiz_id=%d ORDER BY sort ASC;';
    foreach ($wpdb->get_results(sprintf($questionsSQL, $quizID), ARRAY_A) as $index => $row) {
        $arQuizQuestions[$row['id']] = $row;
    }

    // quiz custom fields
    $customfieldsSQL = 'SELECT form_id AS id, fieldname AS name FROM cibq_wp_pro_quiz_form WHERE quiz_id=%d ORDER BY sort ASC;';
    foreach ($wpdb->get_results(sprintf($customfieldsSQL, $quizID), ARRAY_A) as $index => $row) {
        $arQuizCustomFields[$row['id']] = $row['name'];

        // adding quiz custom fields to CSV columns
        $arExportFields['custom_field_' . $row['id'] . ':_' . str_replace(' ', '_', str_replace('"', '""', $row['name']))] = $row['id'];
    }

    // quiz answers
    $exportSQL = "SELECT
                                qs.question_id AS question_id,
                                qr.statistic_ref_id AS user_id,
                                qr.user_id AS user_wp_id,
                                qr.create_time AS quiz_completed_at,
                                qs.correct_count AS answer_correct,
                                qs.incorrect_count AS answer_incorrect,
                                qs.solved_count AS answer_solved,
                                qs.points AS answer_points,
                                qs.question_time AS answer_time,
                                qs.answer_data AS answers,
                                qr.form_data AS quiz_custom_fields_values
                            FROM
                            cibq_wp_pro_quiz_statistic_ref AS qr
                                INNER JOIN cibq_wp_pro_quiz_statistic AS qs ON qr.statistic_ref_id = qs.statistic_ref_id
                            WHERE
                                qr.quiz_id = %d
                            ORDER BY qr.statistic_ref_id ASC , qs.question_id ASC;";

    foreach ($wpdb->get_results(sprintf($exportSQL, $quizID), ARRAY_A) as $index => $row) {
        $arQuizExport[$row['user_id']][$row['question_id']] = $row;
    }

    if (isset($arQuizExport) && !empty($arQuizExport)) {
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=WPProQuizExport (' . date('Y.m.d H.i.s') . ') ' . $arExportFields['quiz_name'] . '.csv');

        $outputCSV = fopen('php://output', 'w');

        // CSV header (column names)
        fputcsv($outputCSV, str_replace('_', ' ', array_keys($arExportFields)));

        foreach ($arQuizExport as $userID => $arQuestions) {
            foreach ($arQuestions as $questionID => $arAnswer) {
                $arCSVRow = [
                    /* quiz_id                */ $arExportFields['quiz_id'],
                    /* quiz_name              */ $arExportFields['quiz_name'],
                    /* quiz_url               */ $arExportFields['quiz_url'],
                    /* question_id            */ $questionID,
                    /* question_title         */ $arQuizQuestions[$questionID]['title'],
                    /* question_url           */ sprintf($arURLs['question_edit'], $arExportFields['quiz_id'], $questionID),
                    /* user_id                */ $arAnswer['user_id'],
                    /* user_wp_id             */ ($arAnswer['user_wp_id'] > 0) ? $arAnswer['user_wp_id'] : 'n.a.',
                    /* user_wp_url            */ ($arAnswer['user_wp_id'] > 0) ? sprintf($arURLs['wp_user_edit'], $arAnswer['user_wp_id']) : 'n.a.',
                    /* user_quiz_completed_at */ date('d/m/Y H:i:s', $arAnswer['quiz_completed_at']),
                    /* answer_correct         */ $arAnswer['answer_correct'],
                    /* answer_incorrect       */ $arAnswer['answer_incorrect'],
                    /* answer_solved          */ $arAnswer['answer_solved'],
                    /* answer_points          */ $arAnswer['answer_points'],
                    /* answer_time            */ gmdate("H:i:s", $arAnswer['answer_time']),
                    /* answers                */ implode(',', json_decode($arAnswer['answers'], true))
                ];

                // adding custom fields values
                if (isset($arQuizCustomFields) && !empty($arQuizCustomFields)) {
                    $arQuizCustomFieldsValues = json_decode($arAnswer['quiz_custom_fields_values'], true);

                    foreach ($arQuizCustomFields as $fieldID => $fieldName) {
                        array_push($arCSVRow, (isset($arQuizCustomFieldsValues[$fieldID])) ? $arQuizCustomFieldsValues[$fieldID] : 'n.a.');
                    }
                }

                fputcsv($outputCSV, $arCSVRow);
            }
        }
    }
}
else {
    die('unauthroized access attempt');
}