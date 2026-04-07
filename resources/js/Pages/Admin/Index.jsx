import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Index({ pendingSubmissions }) {
    const approve = (submissionId) => {
        router.patch(route('admin.approve', submissionId));
    };

    const reject = (submissionId) => {
        router.patch(route('admin.reject', submissionId));
    };

    const remove = (submissionId) => {
        if (confirm('Are you sure you want to delete this submission?')) {
            router.delete(route('admin.destroy', submissionId));
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Admin Queue
                </h2>
            }
        >
            <Head title="Admin" />

            <div className="py-12">
                <div className="mx-auto max-w-6xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        {pendingSubmissions.length === 0 ? (
                            <p className="text-gray-600">No pending submissions right now.</p>
                        ) : (
                            <div className="space-y-4">
                                {pendingSubmissions.map((submission) => (
                                    <div key={submission.id} className="rounded-lg border border-gray-200 p-5">
                                        <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                            <div>
                                                <h3 className="text-lg font-semibold text-gray-900">
                                                    {submission.title}
                                                </h3>

                                                <p className="mt-1 text-sm text-gray-500">
                                                    Board: {submission.board?.name ?? 'Unknown'} | Submitted by {submission.user?.name ?? 'Unknown'}
                                                </p>

                                                <p className="mt-2 text-xs uppercase tracking-wide text-gray-500">
                                                    {submission.type}
                                                </p>
                                            </div>

                                            <div className="flex flex-wrap gap-2">
                                                <button
                                                    onClick={() => approve(submission.id)}
                                                    className="rounded bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700"
                                                >
                                                    Approve
                                                </button>

                                                <button
                                                    onClick={() => reject(submission.id)}
                                                    className="rounded bg-yellow-500 px-4 py-2 text-sm text-white hover:bg-yellow-600"
                                                >
                                                    Reject
                                                </button>

                                                <button
                                                    onClick={() => remove(submission.id)}
                                                    className="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>

                                        {submission.content && (
                                            <p className="mt-4 whitespace-pre-line text-sm text-gray-700">
                                                {submission.content}
                                            </p>
                                        )}

                                        {submission.type === 'poster' && submission.file_path && (
                                            <img
                                                src={`/storage/${submission.file_path}`}
                                                alt={submission.title}
                                                className="mt-4 max-h-[420px] w-full rounded-lg border object-cover"
                                            />
                                        )}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
