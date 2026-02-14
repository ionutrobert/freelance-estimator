# 🚀 Freelance Proposal Time Estimator

An AI-powered SaaS tool for freelance developers to transform messy client briefs into professional, accurate, and exportable project estimates in seconds.

Built with **Laravel 12**, **Livewire 3**, and powered by cutting-edge LLMs (Groq, Google Gemini, xAI, OpenRouter).

Demo Deployment: https://freelance-estimator.onrender.com


![Hero Screenshot](public/screenshots/Screenshot%202026-02-14%20161441.png)

## ✨ Features

- **Brain-Dead Simple Estimation**: Paste a client brief, get a task-by-task breakdown.
- **Multi-LLM Support**: Switch between Groq (Llama 3), Google Gemini, xAI (Grok), and OpenRouter models on the fly.
- **Professional Exports**: Generate client-ready PDF documents or email-safe HTML summaries.
- **Smart History**: All your past estimates are saved and easily searchable via a personal dashboard.
- **Custom Rates**: Set your hourly rate and currency (USD, EUR, RON, etc.) once and use it everywhere.
- **Privacy First**: Choose to keep technical "Internal Notes" that won't show up on client-facing exports.
- **Social Auth**: Quick login via Google or GitHub.

## 📸 Screenshots

| 🚀 Estimation Generation Breakdown |
|---|
| ![Estimation View](public/screenshots/Screenshot%202026-02-14%20161441.png) |

| Estimator Form | Dashboard |
|---|---|
| ![Estimator Form](public/screenshots/Screenshot%202026-02-14%20161228.png) | ![Dashboard](public/screenshots/Screenshot%202026-02-14%20161220.png) |

| PDF Export | Copy to Email (HTML) |
|---|---|
| ![PDF Export](public/screenshots/Screenshot%202026-02-14%20161330.png) | ![Copy to Email](public/screenshots/Screenshot%202026-02-14%20160109.png) |

| Settings & AI Configuration |
|---|
| ![Settings](public/screenshots/Screenshot%202026-02-14%20161238.png) |

## 🛠️ Tech Stack

- **Framework**: [Laravel 12](https://laravel.com)
- **Frontend Components**: [Livewire 3](https://livewire.laravel.com)
- **Styling**: [Tailwind CSS](https://tailwindcss.com)
- **Authentication**: [Laravel Socialite](https://laravel.com/docs/socialite)
- **AI Integrations**: Groq, Google Generative AI, OpenRouter, xAI

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (or your preferred DB)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/freelance-estimator.git
   cd freelance-estimator
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Setup Database**
   ```bash
   # Make sure you have an empty database.sqlite in /database if using SQLite
   touch database/database.sqlite
   php artisan migrate
   ```

5. **Configure AI Keys**
   Add your API keys to the `.env` file:
   ```env
   GROQ_API_KEY=your_key_here
   GOOGLE_API_KEY=your_key_here
   ```

6. **Run the App**
   ```bash
   # Terminal 1
   php artisan serve

   # Terminal 2
   npm run dev
   ```

## 🔒 Security

We take your privacy seriously. **Never** commit your `.env` file to GitHub. This app includes a `.gitignore` that prevents sensitive API keys from being leaked.

## 🤝 Contributing

Got an idea? Fork it and submit a PR! Let's build the best tool for freelancers together.

## 📄 License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
