import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Index({ invitations }) {
    const { auth } = usePage().props;

    const acceptInvitation = (id) => {
        router.post(route('boards.invitations.accept', id));
    };

    const declineInvitation = (id) => {
        router.post(route('boards.invitations.decline', id));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Invitations</h2>}
        >
            <Head title="Invitations" />

            <div className="py-12">
                <div className="mx-auto max-w-4xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 className="mb-4 text-lg font-semibold text-gray-900">Pending Invitations</h3>

                        {invitations.length === 0 ? (
                            <p className="text-gray-600">You have no pending invitations.</p>
                        ) : (
                            <div className="space-y-4">
                                {invitations.map((invitation) => (
                                    <div
                                        key={invitation.id}
                                        className="flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
                                    >
                                        <div>
                                            <div className="font-medium text-gray-900">{invitation.board_name}</div>
                                            <div className="text-sm text-gray-500">
                                                Invited by {invitation.invited_by ?? 'Unknown'}
                                            </div>
                                        </div>

                                        <div className="flex gap-2">
                                            <button
                                                onClick={() => acceptInvitation(invitation.id)}
                                                className="rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                                            >
                                                Accept
                                            </button>

                                            <button
                                                onClick={() => declineInvitation(invitation.id)}
                                                className="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                                            >
                                                Decline
                                            </button>
                                        </div>
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