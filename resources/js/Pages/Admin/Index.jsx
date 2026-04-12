import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';

function SubmissionCard({ submission, showReason = false }) {
    const approve = () => {
        router.patch(route('admin.approve', submission.id));
    };

    const reject = () => {
        router.patch(route('admin.reject', submission.id));
    };

    const destroySubmission = () => {
        if (confirm('Are you sure you want to delete this submission?')) {
            router.delete(route('admin.destroy', submission.id));
        }
    };

    return (
        <div className="rounded-xl border bg-white p-5 shadow-sm">
            <div className="flex items-start justify-between gap-4">
                <div>
                    <h3 className="text-lg font-semibold text-gray-900">{submission.title}</h3>
                    <p className="mt-1 text-sm text-gray-600">
                        By {submission.user?.name} on {submission.board?.name}
                    </p>
                    <p className="mt-2 text-sm">
                        <span className="font-medium">Type:</span> {submission.type}
                    </p>
                    <p className="text-sm">
                        <span className="font-medium">Status:</span> {submission.status}
                    </p>

                    {showReason && submission.moderation_reason && (
                        <div className="mt-3 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900">
                            <span className="font-semibold">Moderation reason:</span>{' '}
                            {submission.moderation_reason}
                        </div>
                    )}

                    {submission.content && (
                        <div className="mt-3 rounded-lg bg-gray-50 p-3 text-sm text-gray-700">
                            {submission.content}
                        </div>
                    )}

                    {submission.type === 'poster' && submission.file_path && (
                        <img
                            src={`/storage/${submission.file_path}`}
                            alt={submission.title}
                            className="mt-4 w-full max-w-md rounded-lg border object-cover"
                        />
                    )}
                </div>
            </div>

            <div className="mt-4 flex flex-wrap gap-3">
                <button
                    onClick={approve}
                    className="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                >
                    Approve
                </button>

                <button
                    onClick={reject}
                    className="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700"
                >
                    Reject
                </button>

                <button
                    onClick={destroySubmission}
                    className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                >
                    Delete
                </button>
            </div>
        </div>
    );
}

export default function Index({ pendingSubmissions, flaggedSubmissions, rejectedSubmissions }) {
    const { auth } = usePage().props;

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Admin Dashboard" />

            <div className="py-10">
                <div className="mx-auto max-w-6xl space-y-10 px-4 sm:px-6 lg:px-8">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                        <p className="mt-2 text-gray-600">
                            Review pending, flagged, and rejected submissions.
                        </p>
                    </div>

                    <section>
                        <h2 className="mb-4 text-2xl font-semibold text-gray-900">
                            Flagged Submissions
                        </h2>

                        {flaggedSubmissions.length > 0 ? (
                            <div className="grid gap-6">
                                {flaggedSubmissions.map((submission) => (
                                    <SubmissionCard
                                        key={submission.id}
                                        submission={submission}
                                        showReason={true}
                                    />
                                ))}
                            </div>
                        ) : (
                            <div className="rounded-xl border bg-white p-6 text-gray-600 shadow-sm">
                                No flagged submissions.
                            </div>
                        )}
                    </section>

                    <section>
                        <h2 className="mb-4 text-2xl font-semibold text-gray-900">
                            Pending Submissions
                        </h2>

                        {pendingSubmissions.length > 0 ? (
                            <div className="grid gap-6">
                                {pendingSubmissions.map((submission) => (
                                    <SubmissionCard
                                        key={submission.id}
                                        submission={submission}
                                    />
                                ))}
                            </div>
                        ) : (
                            <div className="rounded-xl border bg-white p-6 text-gray-600 shadow-sm">
                                No pending submissions.
                            </div>
                        )}
                    </section>

                    <section>
                        <h2 className="mb-4 text-2xl font-semibold text-gray-900">
                            Rejected Submissions
                        </h2>

                        {rejectedSubmissions.length > 0 ? (
                            <div className="grid gap-6">
                                {rejectedSubmissions.map((submission) => (
                                    <SubmissionCard
                                        key={submission.id}
                                        submission={submission}
                                        showReason={true}
                                    />
                                ))}
                            </div>
                        ) : (
                            <div className="rounded-xl border bg-white p-6 text-gray-600 shadow-sm">
                                No rejected submissions.
                            </div>
                        )}
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}