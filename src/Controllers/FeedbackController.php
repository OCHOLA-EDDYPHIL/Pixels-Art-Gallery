<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\FeedbackService;
use App\Utils\Csrf;
use App\Utils\Response;
use App\Utils\Session;

/**
 * Visitor feedback form (CNS 3205 Lab 2, Part B #7).
 *
 * GET  /feedback  -> render the form plus all stored entries
 * POST /feedback  -> validate + store, then redirect back (PRG pattern)
 */
final class FeedbackController extends BaseController
{
    /**
     * @param \Closure(): FeedbackService $feedbackFactory
     */
    public function __construct(private readonly \Closure $feedbackFactory)
    {
    }

    public function index(): Response
    {
        Session::start();

        $errors = $_SESSION['feedback_errors'] ?? [];
        $success = $_SESSION['feedback_success'] ?? null;
        unset($_SESSION['feedback_errors'], $_SESSION['feedback_success']);

        $entries = ($this->feedbackFactory)()->all();

        return Response::html($this->render('feedback/index', [
            'csrfToken' => Csrf::token(),
            'errors' => $errors,
            'success' => $success,
            'entries' => $entries,
            'currentUserEmail' => $_SESSION['email'] ?? null,
        ]));
    }

    /**
     * @param array<string, mixed> $input
     */
    public function submit(array $input): Response
    {
        Session::start();

        if (!Csrf::verify((string) ($input['csrf_token'] ?? ''))) {
            return Response::text('Invalid CSRF token', 403);
        }

        $errors = ($this->feedbackFactory)()->submit(
            (string) ($input['name'] ?? ''),
            (string) ($input['email'] ?? ''),
            (string) ($input['message'] ?? '')
        );

        if (!empty($errors)) {
            $_SESSION['feedback_errors'] = $errors;
        } else {
            $_SESSION['feedback_success'] = 'Thank you! Your feedback was saved.';
        }

        return Response::redirect('/feedback');
    }
}
