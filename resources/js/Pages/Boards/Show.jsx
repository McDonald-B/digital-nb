import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

function MemberRow({ member, board, isOwner }) {
  const promote = () => {
    if (confirm(`Promote ${member.name} to board admin?`)) {
      router.patch(route('boards.members.promote', [board.id, member.id]));
    }
  };

  const demote = () => {
    if (confirm(`Demote ${member.name} back to member?`)) {
      router.patch(route('boards.members.demote', [board.id, member.id]));
    }
  };

  const remove = () => {
    if (confirm(`Remove ${member.name} from this board?`)) {
      router.delete(route('boards.members.remove', [board.id, member.id]));
    }
  };

  const transferOwnership = () => {
    if (confirm(`Transfer ownership of this board to ${member.name}?`)) {
      router.patch(route('boards.transferOwnership', [board.id, member.id]));
    }
  };

  return (
    <div className="flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div className="font-medium text-gray-900">{member.name}</div>
        <div className="text-sm text-gray-500">{member.email}</div>

        <div className="mt-2 flex flex-wrap gap-2">
          {member.is_owner ? (
            <span className="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
              Owner
            </span>
          ) : member.role === 'admin' ? (
            <span className="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
              Board Admin
            </span>
          ) : (
            <span className="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
              Member
            </span>
          )}
        </div>
      </div>

      {isOwner && !member.is_owner && (
        <div className="flex flex-wrap gap-2">
          {member.role === 'member' ? (
            <button
              onClick={promote}
              className="rounded bg-amber-600 px-3 py-2 text-sm font-medium text-white hover:bg-amber-700"
            >
              Promote to Admin
            </button>
          ) : (
            <button
              onClick={demote}
              className="rounded bg-gray-700 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800"
            >
              Demote to Member
            </button>
          )}

          <button
            onClick={transferOwnership}
            className="rounded bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700"
          >
            Transfer Ownership
          </button>

          <button
            onClick={remove}
            className="rounded bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700"
          >
            Remove
          </button>
        </div>
      )}
    </div>
  );
}

export default function Show({
  board,
  submissions,
  isMember,
  isBoardAdmin,
  isOwner,
  memberCount,
  members,
  pendingInvitations,
}) {
  const { auth } = usePage().props;
  const isGlobalAdmin = auth.user?.role === 'admin';

  const { data, setData, post, processing, reset, errors } = useForm({
    email: '',
  });

  const submitInvite = (e) => {
    e.preventDefault();
    post(route('boards.invite', board.id), {
      onSuccess: () => reset(),
    });
  };

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

                {isOwner && board.is_private && (
                  <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900">Invite Members</h3>

                    <form onSubmit={submitInvite} className="flex flex-col gap-4 sm:flex-row">
                      <div className="flex-1">
                        <input
                          type="email"
                          value={data.email}
                          onChange={(e) => setData('email', e.target.value)}
                          placeholder="Enter user email"
                          className="w-full rounded border border-gray-300 px-3 py-2"
                        />
                        {errors.email && (
                          <div className="mt-1 text-sm text-red-600">{errors.email}</div>
                        )}
                      </div>

                      <button
                        type="submit"
                        disabled={processing}
                        className="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 disabled:opacity-50"
                      >
                        Send Invitation
                      </button>
                    </form>

                    <div className="mt-6">
                      <h4 className="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-700">
                        Pending Invitations
                      </h4>

                      {pendingInvitations.length === 0 ? (
                        <p className="text-sm text-gray-500">No pending invitations.</p>
                      ) : (
                        <div className="space-y-3">
                          {pendingInvitations.map((invitation) => (
                            <div
                              key={invitation.id}
                              className="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3"
                            >
                              <div className="font-medium text-gray-900">
                                {invitation.name ?? 'Unknown user'}
                              </div>
                              <div className="text-sm text-gray-500">
                                {invitation.email ?? 'No email'}
                              </div>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>
                  </div>
                )}

                <div className="mt-2 text-sm text-gray-500">
                  Members: {memberCount}
                </div>

                <div className="mt-3 flex flex-wrap gap-2">
                  {isOwner && (
                    <span className="inline-block rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                      Owner
                    </span>
                  )}

                  {isBoardAdmin && !isOwner && (
                    <span className="inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                      Board Admin
                    </span>
                  )}

                  {isMember && !isBoardAdmin && !isOwner && (
                    <span className="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                      Member
                    </span>
                  )}

                  {!isMember && (
                    <span className="inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                      Not Joined
                    </span>
                  )}
                </div>

                <div className="mt-3 text-sm text-gray-500">
                  {isOwner
                    ? 'You own this board.'
                    : isBoardAdmin
                      ? 'You are an admin of this board.'
                      : isMember
                        ? 'You are a member of this board.'
                        : 'You are not a member of this board yet.'}
                </div>
              </div>

              <div className="flex flex-wrap gap-3">
                {isMember ? (
                  <>
                    <Link
                      href={route('submissions.create', board.id)}
                      className="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
                    >
                      Submit Notice
                    </Link>

                    {!isOwner && (
                      <button
                        onClick={() => {
                          if (confirm('Are you sure you want to leave this board?')) {
                            router.delete(route('boards.leave', board.id));
                          }
                        }}
                        className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                      >
                        Leave Board
                      </button>
                    )}
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
            <h3 className="mb-4 text-lg font-semibold text-gray-900">Members</h3>

            {members.length === 0 ? (
              <p className="text-gray-600">No members found.</p>
            ) : (
              <div className="space-y-4">
                {members.map((member) => (
                  <MemberRow
                    key={member.id}
                    member={member}
                    board={board}
                    isOwner={isOwner}
                  />
                ))}
              </div>
            )}
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
                        <div className="mb-2 flex items-center gap-2">
                          <span className="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                            Approved
                          </span>
                          <span className="text-sm text-gray-500">
                            By {submission.user?.name ?? 'Unknown'}
                          </span>
                        </div>

                        <h4 className="text-lg font-semibold text-gray-900">{submission.title}</h4>
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