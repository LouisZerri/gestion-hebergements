'use client';

import { useState } from 'react';
import {
  Box,
  Button,
  HStack,
  VStack,
  Image,
  Text,
  IconButton,
  SimpleGrid,
  Input,
} from '@chakra-ui/react';
import { showToast } from '@/lib/toast';
import { apiService } from '@/lib/api';
import type { HotelPicture } from '@/types/api';

interface PhotoManagerProps {
  hotelId?: number; // undefined si création
  pictures: HotelPicture[];
  onPhotosChange: (pictures: HotelPicture[]) => void;
}

export default function PhotoManager({ hotelId, pictures, onPhotosChange }: PhotoManagerProps) {
  const [uploading, setUploading] = useState(false);
  const [selectedFiles, setSelectedFiles] = useState<File[]>([]);

  const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const files = Array.from(e.target.files);
      
      // Validation
      const validFiles = files.filter(file => {
        if (!file.type.startsWith('image/')) {
          showToast.error(`${file.name} n'est pas une image`);
          return false;
        }
        if (file.size > 5 * 1024 * 1024) {
          showToast.error(`${file.name} est trop volumineux (max 5MB)`);
          return false;
        }
        return true;
      });

      setSelectedFiles(validFiles);
    }
  };

  const handleUpload = async () => {
  if (!hotelId) {
    showToast.error('Veuillez d\'abord créer l\'hôtel avant d\'ajouter des photos');
    return;
  }

  if (selectedFiles.length === 0) {
    showToast.error('Veuillez sélectionner des fichiers');
    return;
  }

  setUploading(true);
  try {
    const response = await apiService.uploadPictures(hotelId, selectedFiles);
    
    console.log('Upload response:', response); // Debug
    
    if (response.success && response.data) {
      showToast.success(`${selectedFiles.length} photo(s) uploadée(s) avec succès`);
      
      // Recharger l'hôtel pour avoir les photos à jour
      const hotelResponse = await apiService.getHotel(hotelId);
      if (hotelResponse.success && hotelResponse.data) {
        onPhotosChange(hotelResponse.data.pictures || []);
      }
      
      setSelectedFiles([]);
      // Reset input
      const input = document.getElementById('photo-upload') as HTMLInputElement;
      if (input) input.value = '';
    }
  } catch (error: any) {
    console.error('Upload error:', error); // Debug
    showToast.error(error.message || 'Erreur lors de l\'upload');
  } finally {
    setUploading(false);
  }
};

  const handleDelete = async (pictureId: number) => {
    if (!hotelId) return;

    try {
      await apiService.deletePicture(hotelId, pictureId);
      showToast.success('Photo supprimée avec succès');
      onPhotosChange(pictures.filter(p => p.id !== pictureId));
    } catch (error: any) {
      showToast.error(error.message || 'Erreur lors de la suppression');
    }
  };

  const handleMoveUp = async (index: number) => {
    if (index === 0 || !hotelId) return;
    
    const picture = pictures[index];
    const newPosition = pictures[index - 1].position;

    try {
      await apiService.updatePicturePosition(hotelId, picture.id, newPosition);
      
      // Réorganiser localement
      const newPictures = [...pictures];
      [newPictures[index - 1], newPictures[index]] = [newPictures[index], newPictures[index - 1]];
      onPhotosChange(newPictures);
    } catch (error: any) {
      showToast.error('Erreur lors du déplacement');
    }
  };

  const handleMoveDown = async (index: number) => {
    if (index === pictures.length - 1 || !hotelId) return;
    
    const picture = pictures[index];
    const newPosition = pictures[index + 1].position;

    try {
      await apiService.updatePicturePosition(hotelId, picture.id, newPosition);
      
      // Réorganiser localement
      const newPictures = [...pictures];
      [newPictures[index], newPictures[index + 1]] = [newPictures[index + 1], newPictures[index]];
      onPhotosChange(newPictures);
    } catch (error: any) {
      showToast.error('Erreur lors du déplacement');
    }
  };

  return (
    <VStack gap={4} align="stretch">
      <Box>
        <Text fontWeight="bold" mb={2}>Photos de l'hôtel</Text>
        {!hotelId && (
          <Text fontSize="sm" color="orange.600" mb={2}>
            ⚠️ Vous pourrez ajouter des photos après avoir créé l'hôtel
          </Text>
        )}
      </Box>

      {/* Upload de nouvelles photos */}
      {hotelId && (
        <HStack>
          <Input
            id="photo-upload"
            type="file"
            accept="image/*"
            multiple
            onChange={handleFileSelect}
            disabled={uploading}
          />
          <Button
            colorScheme="green"
            onClick={handleUpload}
            loading={uploading}
            disabled={selectedFiles.length === 0}
          >
            Upload {selectedFiles.length > 0 && `(${selectedFiles.length})`}
          </Button>
        </HStack>
      )}

      {/* Liste des photos existantes */}
      {pictures.length > 0 ? (
        <SimpleGrid columns={{ base: 2, md: 3, lg: 4 }} gap={4}>
          {pictures.map((picture, index) => (
            <Box
              key={picture.id}
              borderWidth="1px"
              borderRadius="lg"
              overflow="hidden"
              position="relative"
            >
              <Image
                src={apiService.getImageUrl(picture.filepath)}
                alt={`Photo ${index + 1}`}
                w="full"
                h="150px"
                objectFit="cover"
              />
              
              <VStack
                position="absolute"
                top={2}
                right={2}
                gap={1}
              >
                {/* Bouton monter */}
                {index > 0 && (
                  <IconButton
                    size="xs"
                    colorScheme="blue"
                    aria-label="Monter"
                    onClick={() => handleMoveUp(index)}
                  >
                    ↑
                  </IconButton>
                )}
                
                {/* Bouton descendre */}
                {index < pictures.length - 1 && (
                  <IconButton
                    size="xs"
                    colorScheme="blue"
                    aria-label="Descendre"
                    onClick={() => handleMoveDown(index)}
                  >
                    ↓
                  </IconButton>
                )}
                
                {/* Bouton supprimer */}
                <IconButton
                  size="xs"
                  colorScheme="red"
                  aria-label="Supprimer"
                  onClick={() => handleDelete(picture.id)}
                >
                  ×
                </IconButton>
              </VStack>

              <Box p={2} bg="gray.50">
                <Text fontSize="xs" color="gray.600">
                  Position {picture.position + 1}
                </Text>
              </Box>
            </Box>
          ))}
        </SimpleGrid>
      ) : (
        <Box
          borderWidth="2px"
          borderStyle="dashed"
          borderRadius="lg"
          p={8}
          textAlign="center"
          color="gray.500"
        >
          <Text>Aucune photo</Text>
        </Box>
      )}
    </VStack>
  );
}