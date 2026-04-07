import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Show({ board, submissions, isMember }) {
  const { auth } = usePage().props;
  const isGlobalAdmin = auth.user?.role === 'admin';

  return (
    <AuthenticatedLayout
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800">
          {board.name}
        </h2>
      }
    >
      <Head title={board.name} />

      <div className="py-12">
        <div className="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
          <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
            <div className="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
              <div>
                <p className="text-gray-700">{board.description || 'No description yet.'}</p>

                <div className="mt-4 text-sm text-gray-500">
                  Created by: {board.owner?.name ?? 'Unknown'}
                </div>

                <div className="mt-2 text-sm text-gray-500">
                  {isMember
                    ? 'You are a member of this board.'
                    : 'You are not a member of this board yet.'}
                </div>
              </div>

              <div className="flex flex-wrap gap-3">
                {isMember ? (
                  <>
                    <span className="inline-block rounded bg-green-100 px-3 py-2 text-sm text-green-700">
                      Member
                    </span>

                    <Link
                      href={route('submissions.create', board.id)}
                      className="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
                    >
                      Submit Notice
                    </Link>
                  </>
                ) : (
                  <button
                    onClick={() => router.post(route('boards.join', board.id))}
                    className="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                  >
                    Join Board
                  </button>
                )}
              </div>
            </div>
          </div>

          <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
            <h3 className="mb-4 text-lg font-semibold text-gray-900">Submissions</h3>

            {submissions.length === 0 ? (
              <p className="text-gray-600">No submissions available yet.</p>
            ) : (
              <div className="space-y-4">
                {submissions.map((submission) => (
                  <div key={submission.id} className="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                      <div>
                        <h4 className="text-lg font-semibold text-gray-900">{submission.title}</h4>
                        <p className="mt-1 text-sm text-gray-500">
                          Posted by {submission.user?.name ?? 'Unknown'}
                        </p>
                      </div>

                      <div className="flex gap-2">
                        <span className="rounded bg-gray-100 px-2 py-1 text-xs uppercase text-gray-700">
                          {submission.type}
                        </span>

                        {isGlobalAdmin && (
                          <span
                            className={
                              'rounded px-2 py-1 text-xs uppercase ' +
                              (submission.status === 'approved'
                                ? 'bg-green-100 text-green-700'
                                : submission.status === 'pending'
                                  ? 'bg-yellow-100 text-yellow-700'
                                  : 'bg-red-100 text-red-700')
                            }
                          >
                            {submission.status}
                          </span>
                        )}
                      </div>
                    </div>

                    {submission.content && (
                      <p className="mt-4 whitespace-pre-line text-gray-700">
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
