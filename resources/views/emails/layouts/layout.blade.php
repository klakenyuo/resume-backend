<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f9fafb;
        color: #111827;
    }
    .email-container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .header {
        background-color: #111827;
        color: #ffffff;
        text-align: center;
        padding: 20px;
    }
    .header h1 {
        margin: 0;
        font-size: 26px;
    }
    .content {
        padding: 30px 20px;
        text-align: center;
    }
    .content h2 {
        margin-bottom: 15px;
        font-size: 22px;
        color: #111827;
    }
    .content p {
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 20px;
        color: #4b5563;
    }
    .code-block {
        display: inline-block;
        background-color: #f3f4f6;
        color: #111827;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 20px;
        margin-bottom: 20px;
        letter-spacing: 2px;
    }
    .reset-button {
        display: inline-block;
        background-color: #111827;
        color: #ffffff;
        text-decoration: none;
        padding: 12px 30px;
        border-radius: 5px;
        font-weight: bold;
        margin: 20px auto;
        font-size: 16px;
    }
    .reset-button:hover {
        background-color: #374151;
    }
    .footer {
        background-color: #f9fafb;
        text-align: center;
        padding: 20px;
        font-size: 14px;
        color: #6b7280;
    }
    .footer div {
        margin-bottom: 10px;
    }
    .footer a {
        color: #111827;
        font-weight: bold;
        text-decoration: none;
    }
    .footer a:hover {
        text-decoration: underline;
    }
    /* Responsive Design */
    @media (max-width: 600px) {
        .content h2 {
            font-size: 20px;
        }
        .content p {
            font-size: 14px;
        }
    }
</style>

@include('emails.layouts.header')

<!-- Content -->
<div class="content">
    @yield('content')
</div>

@include('emails.layouts.footer')