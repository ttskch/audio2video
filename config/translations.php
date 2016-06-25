<?php
$app['translator.domains'] = [
    'messages' => [
        'ja' => [
            'Audio file' => '音声ファイル',
            'Output format' => '出力動画のファイル形式',
            'Frame rate' => '出力動画のフレームレート (fps)',
            'Image file' => '画像ファイル',
            'Resolution' => '解像度',
            'Background Color' => '背景色',
            'Convert' => '変換',

            'index_subtitle' => '音声ファイルを動画ファイルに変換',
            'meta_description' => 'mp3などの音声ファイルを、静止画付きの動画ファイルに変換することができます。Twitterへ音声ファイルの投稿などにご活用ください。',
            'lead_description' => 'mp3などの音声ファイルを、静止画付きの動画ファイルに変換することができます。Twitterへ音声ファイルの投稿などにご活用ください 😃',
            'advanced_settings' => '詳細設定',
            'use_image_file' => '画像ファイルを使う',
            'generate_image' => '画像を生成する',
            'audio_duration_warning' => '再生時間が140秒を超える音声ファイルは入力できません',
            'exception_too_long_duration' => '音声ファイルの再生時間が140秒を超えていたため変換に失敗しました。',
        ],
        'en' => [
            'index_subtitle' => 'Convert your audio file to video file',
            'meta_description' => "You can convert audio file like mp3 into an audio-only movie file. It's maybe useful fot attaching it to your tweets.",
            'lead_description' => "You can convert audio file like mp3 into an audio-only movie file. It's maybe useful fot attaching it to your tweets :)",
            'advanced_settings' => 'Advanced',
            'use_image_file' => 'Use image file',
            'generate_image' => 'Generate image',
            'audio_duration_warning' => 'Duration time of audio file is allowed up to 140 sec',
            'exception_too_long_duration' => 'Duration time of audio file was over 140 sec.',
        ],
    ],
];
