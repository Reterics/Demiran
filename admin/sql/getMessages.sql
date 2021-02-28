SELECT id, `source`, target, message, created, `status`
FROM messages
WHERE id IN (
  SELECT MAX(id)
  FROM messages
  GROUP BY source, target
);

SELECT id, `source`, target, message, created, `status`
FROM messages
WHERE id IN (
  SELECT MAX(messages.id)
  FROM messages INNER JOIN users ON messages.target=users.id OR messages.source=users.id
  GROUP BY source, target
)

