<?php

namespace App\DAO;

use Doctrine\DBAL\Connection;

class UtilisateurDAO
{
    private \PDO $pdo;

    public function __construct(Connection $connection)
    {
        // On récupère l'accès natif à PDO
        $this->pdo = $connection->getNativeConnection();
    }

    /**
     * Récupère tous les utilisateurs
     * @return array La liste de tous les utilisateurs (tableaux associatifs)
     */
    public function getAllUtilisateurs(): array
    {
        $statement = $this->pdo->query('SELECT * FROM utilisateur');
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un seul utilisateur grâce à son ID
     * @param int $id L'identifiant de l'utilisateur (utilisateur_id)
     * @return array|null Les données de l'utilisateur, ou null si introuvable
     */
    public function getUtilisateurById(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM utilisateur WHERE utilisateur_id = :id');
        $statement->execute(['id' => $id]);
        
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }


    /**
     * Récupère un utilisateur via son email (login)
     * @param string $email L'email tapé lors de la connexion
     * @return array|null Les données de l'utilisateur, ou null si introuvable
     */
    public function getUtilisateurByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM utilisateur WHERE email = :email');
        $statement->execute(['email' => $email]);
        
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

/**
 * Récupère le mot de passe (login)
 * @param string $email email lié au compte utilisateur
 * @return string|null le mot de passe haché ou null
 */
public function getpassword(string $email): ?string
{
    $statement = $this->pdo->prepare('SELECT password FROM utilisateur WHERE email = :email');
    $statement->execute(['email' => $email]);

    $result = $statement->fetch(\PDO::FETCH_ASSOC);
    return $result ? $result['password'] : null;
}

    public function getUtilisateurByRole(string $roleName): array
    {
    $sql = 'SELECT utilisateur.* FROM utilisateur
        INNER JOIN role ON utilisateur.role_id = role.role_id
        WHERE role.libelle = :roleName';

    $statement = $this->pdo->prepare($sql);
    $statement->execute(['roleName' => $roleName]);

    return $statement->fetchAll(\PDO::FETCH_ASSOC);

    
    }

    public function ajouterUtilisateur(string $prenom, string $gsm, string $email, string $adresse, string $ville, string $pays, string $passwordHache): bool
    {
        $sql = "INSERT INTO utilisateur (prenom, telephone, email, adresse_postale, ville, pays, password, role_id) 
                VALUES (:prenom, :gsm, :email, :adresse, :ville, :pays, :hashedPassword, '3')";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            ':prenom'   => $prenom,
            ':gsm'      => $gsm,
            ':email'    => $email,
            ':adresse'  => $adresse,
            ':ville'    => $ville,
            ':pays'      => $pays,
            ':hashedPassword' => $passwordHache
        ]);
    }
}


