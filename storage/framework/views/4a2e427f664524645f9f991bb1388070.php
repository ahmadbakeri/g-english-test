<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - <?php echo e($student->name); ?></title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
            text-align: center;
        }
        .certificate-wrapper {
            width: 100%;
            height: 100vh;
            padding: 40px;
            box-sizing: border-box;
            background: #fff; /* Could add a background image here */
            border: 20px solid #0369a1;
            position: relative;
        }
        .inner-border {
            border: 5px double #0369a1;
            width: 100%;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
        }
        .header h1 {
            font-size: 50px;
            color: #0369a1;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 5px;
        }
        .header p {
            font-size: 20px;
            margin-top: 10px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content {
            margin-top: 60px;
        }
        .content .presented-to {
            font-size: 20px;
            margin-bottom: 20px;
            font-style: italic;
        }
        .content .student-name {
            font-size: 45px;
            font-weight: bold;
            color: #000;
            margin-bottom: 20px;
            border-bottom: 2px solid #ccc;
            display: inline-block;
            padding: 0 40px;
        }
        .content .description {
            font-size: 18px;
            line-height: 1.6;
            margin: 20px auto;
            max-width: 800px;
        }
        .footer {
            margin-top: 80px;
            width: 100%;
            position: absolute;
            bottom: 60px;
            left: 0;
        }
        .signature-block {
            display: inline-block;
            width: 300px;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-block.left {
            float: left;
            margin-left: 80px;
        }
        .signature-block.right {
            float: right;
            margin-right: 80px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 100%;
            margin-bottom: 10px;
            height: 60px; /* Space for signature image if needed */
        }
        .signature-name {
            font-size: 18px;
            font-weight: bold;
        }
        .signature-title {
            font-size: 14px;
            color: #555;
        }
        .badge {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 120px;
            background-color: #0369a1;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 0 0 2px #0369a1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }
        .badge-text {
            margin-top: 35px;
        }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="inner-border">
            <div class="header">
                <h1>Certificate of Completion</h1>
                <p><?php echo e(\App\Models\Setting::where('key', 'institute_name')->value('value') ?? 'G-English Course'); ?></p>
            </div>

            <div class="content">
                <div class="presented-to">This is proudly presented to</div>
                <div class="student-name"><?php echo e($student->name); ?></div>
                <div class="description">
                    For successfully completing the English Language Program with excellent performance.<br>
                    Your dedication and hard work have resulted in a significant improvement in your English skills.
                </div>
            </div>

            <div class="footer">
                <div class="signature-block left">
                    <div class="signature-line"></div>
                    <div class="signature-name">Tutor / Instructor</div>
                    <div class="signature-title">English Department</div>
                </div>
                
                <div class="badge">
                    <div class="badge-text">
                        AWARD OF<br>EXCELLENCE<br><?php echo e(date('Y')); ?>

                    </div>
                </div>

                <div class="signature-block right">
                    <div class="signature-line"></div>
                    <div class="signature-name">Director / Principal</div>
                    <div class="signature-title"><?php echo e(\App\Models\Setting::where('key', 'institute_name')->value('value') ?? 'G-English Course'); ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\ahmad\Downloads\Laravel\g-english\resources\views/reports/certificate.blade.php ENDPATH**/ ?>