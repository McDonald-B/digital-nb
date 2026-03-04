import MainLayout from '@/Layouts/MainLayout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function BoardsIndex({ boards }) {
  const [search, setSearch] = useState('');

  const handleSearch = (e) => {
    e.preventDefault();
    router.get('/boards', { search }, { preserveState: true });
  };

  return (
    <MainLayout>
      <h1 className="text-3xl font-bold text-blue-900 mb-6">Browse Notice Boards</h1>
      <form onSubmit={handleSearch} className="mb-6 flex gap-2">
        <input value={search} onChange={e => setSearch(e.target.value)}
          placeholder="Search boards..."
          className="border rounded px-4 py-2 flex-1" />
        <button type="submit" className="bg-blue-900 text-white px-6 py-2 rounded">Search</button>
      </form>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {boards.map(board => (
          <Link key={board.id} href={`/boards/${board.id}`}
            className="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <h2 className="text-xl font-semibold">{board.name}</h2>
            <p className="text-gray-600 mt-2">{board.description}</p>
            <p className="text-sm text-blue-600 mt-4">By {board.owner.name}</p>
          </Link>
        ))}
      </div>
      <Link href="/boards/create"
        className="fixed bottom-8 right-8 bg-blue-900 text-white px-6 py-3 rounded-full shadow-lg">
        + New Board
      </Link>
    </MainLayout>
  );
}
