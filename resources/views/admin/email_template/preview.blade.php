<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Preview - {{ $emailTemplate->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .preview-header {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .preview-header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #333;
        }
        .preview-header .subject {
            font-size: 16px;
            color: #666;
            margin: 10px 0;
        }
        .preview-body {
            background: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="preview-header">
        <h1>{{ $emailTemplate->name }}</h1>
        <div class="alert">
            <strong>Note:</strong> This is a preview with sample data. Actual emails will have real values.
        </div>
        <div class="subject">
            <strong>Subject:</strong> {{ $subject }}
        </div>
    </div>
    
    <div class="preview-body">
        {!! $body !!}
    </div>
</body>
</html>
